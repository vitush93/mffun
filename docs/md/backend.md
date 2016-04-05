# Backend development

Please refer to the Nette framework's [documentation](http://doc.nette.org/cs/2.3/) for more info.

Application is divided into two modules: *FrontModule* and *AdminModule*.

Application overview (namespaces corresponds with the folder structure):

- `App\*Module\Presenters` namespace - this is where all presenters for the corresponding module are located. All presenters must extend the `App\*Module\BasePresenter` class.
- Templates for presenter (and its actions) are located under `app/*Module/templates` folder.
- `App\Model` namespace contains application's main business logic. Its divided as follows:
  - `App\Model\Entities` Doctrine ORM entities are located here.
  - `App\Model\Events` Doctrine ORM lifecycle event classes for corresponding entities are located here.
  - `App\Model\Repositories` contains classes responsible for more complicated data retrieval and persistence (basic CRUD operations are handled directly in presenters using Doctrine's default repositories.
  - `App\Model\Services` contains service classes.
- Application components are located in `App\*Module\Components` namespace. In case of MFFun there are only two front-end components:
  - **AddQuote** is a component that handles adding quote and displaying the the modal form.
  - **CheckLdap** handles authorizing user using university's LDAP and displaying the modal form.


