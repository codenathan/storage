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
        // TODO: Implement get_validation() method.
    }

    public function getModelName()
    {
        return 'User';
    }

    public function getRequiredFields()
    {
       return ['user'];
    }
}