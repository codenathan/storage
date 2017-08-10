## Model CRUD with custom Storage Implementation

### Intro

- This small app allows you to perform CRUD Requests for custom built storage methods
- You can easily add new classes and implement storage functionality using the interfaces and abstract classes available

### Required

- PHP 7 for Twig 2.0
- Composer
- MySQL if using database storage


### Getting Started 

- Point Document Root to public folder 
- To add new models simply create them under \App\Models\
- To add new storage implementation simply create them under \App\Services\

### Front End 

- Template Engine       -   [Twig 2.0 ](https://twig.symfony.com/doc/2.x/templates.html)
- Front End Framework   -   [Bootstrap v3.3.7](http://getbootstrap.com/getting-started/)
- Javascript Framework  -   [VueJS 2.4.2](https://vuejs.org/v2/guide/#Getting-Started)

#### Folder Structure ( for Front End )

```
views/
├── app.twig        <-- This has the base structure put in place for the html ( can only be extended for other templates to be used)
├── index.twig      <-- Main Homepage view that is rendered 
├── includes/       <-- The main includes folder  
└── errors/         <-- All Error Page Views are added here ( 404 / 501 / etc..)

```