import CategoryController from '../Controllers/CategoryController'

//export class => usable as a module
export default class categoryDirective {
    constructor() {
        //class => use this
        this.restrict       ='E';
        this.replace        = true;
        this.controller     = CategoryController;
        this.controllerAs   = 'CategoryController';
        this.templateUrl    = AngularApp.webDir + 'bundles/cpasimusantesimupoll/js/Category/Partials/category.directive.html';
        this.scope= { tree: '=', sid: '='};
    }
    link(scope, element, attr, CategoryController) {
        CategoryController.init(scope.tree, scope.sid);
    }
}
