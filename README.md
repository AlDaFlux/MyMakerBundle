# MyMakerBundle


## aldaflux_mymaker.yaml

```
my_maker:
    backoffice:
        folder: 
            controller: BackOffice 
            template: backoffice
        route:
            name_prefix: admin_
            path_prefix: admin
    frontoffice:
        folder: 
            controller: Frontoffice
            template: frontoffice
```


## ./bin/console
```

$ ./bin/console make:front:show User

 created: src/Controller/Frontoffice/UserController.php
 created: templates/frontoffice/user/index.html.twig
 created: templates/frontoffice/user/show.html.twig
 
 Success!


 Next: Check your new CRUD by going to /user/

$ ./bin/console make:crud:admin User

 created: src/Controller/BackOffice/UserController.php
 created: src/Form/UserType.php
 created: templates/backoffice/user/_delete_form.html.twig
 created: templates/backoffice/user/_form.html.twig
 created: templates/backoffice/user/edit.html.twig
 created: templates/backoffice/user/index.html.twig
 created: templates/backoffice/user/new.html.twig
 created: templates/backoffice/user/show.html.twig

 Success!

 Next: Check your new CRUD by going to admin/user/

```


```
$ ./bin/console make:crud:admin User --with-voter

```







