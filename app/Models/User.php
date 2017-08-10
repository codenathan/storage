<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {

    /**
     * Alphanumeric with hyphen and underscore only
     *
     * @var
     */
    public $username;

    /**
     * Mr,Mrs,Miss,Rs,Rev,Dr,Professor - Should be conistent with gender
     *
     * @var
     */
    public $title;

    /**
     * Alpha no spaces
     *
     * @var
     */
    public $firstName;


    /**
     * 1 alpha char
     *
     * @var
     */
    public $middleInitial;

    /**
     * Alpha, allow spaces and hyphens
     *
     * @var
     */
    public $lastName;

    /**
     * 1 alpha char, ( M = male, F = female). Should be consistent with traditional gender for title
     *
     * @var
     */
    public $gender;

    /**
     * valid date. YYYY-MM-DD format
     *
     * @var
     */
    public $dateOfBirth;


    public function get_validation()
    {
        return [
            'username'              => '/^[a-zA-Z0-9-_]+$/',
            'title'                 => [
                                        'conditional_model' => 'gender',
                                        'conditional_value' => ['Mr','Mrs','Miss','Ms','Rev','Dr','Professor'],
                                        'conditional_map'   => [
                                                'M'     => ['Mr','Rev','Dr','Professor'],
                                                'F'     => ['Mrs','Miss','Ms','Dr','Professor']
                                            ]
                                        ],
            'firstName'             => '/^[a-z0-9 .\-]+$/i',
            'middleInitial'         => '/^[a-zA-Z]$/',
            'lastName'              => '/^[a-z ,.\'-]+$/i',
            'gender'                => [
                                        'conditional_value' => ['M','F'],
                                        'regex' => '/^[a-zA-Z]$/',
                                        ],
            'dateOfBirth'           => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'

        ];
    }

    public function getModelName()
    {
        return 'User';
    }

    public function getRequiredFields()
    {
       return ['username','title','firstName','lastName','gender','dateOfBirth'];
    }
}