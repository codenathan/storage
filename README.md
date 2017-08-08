## Model CRUD with custom Storage Implementation

### Intro

- This small app allows you to perform CRUD Requests for custom built storage methods
- You can easily add new classes and implement storage functionality using the interfaces and abstract classes

### Required

- PHP 7 for Twig 2.0
- Composer
- MySQL if using database storage


### Getting Started 

- Point Document Root to public folder 
- To add new models simply create them under \App\Models\
- To add new storage implementation simply create them under \App\Services\

### Front End 

- Template Engine use is Twig, more information [here](https://twig.symfony.com/doc/2.x/templates.html)

#### Folder Structure

```
views
│   index.twig      <-- HOMEPAGE
└───errors          <-- Contains all the views for error page 
└───js              <-- put all script files here
└───images          <-- any images in here

                    CREATE a new folder for every model
└───model1
│   └───index.twig  <-- For Listing all model data
│   └───create.twig <-- For creating a new model
│   └───edit.twig   <-- For edit a new model
│  
└───model2
└───model3 etc...
```