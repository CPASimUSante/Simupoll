import categoryController from '../Controllers/categoryController'
//export class => usable as a module
export default class category {
    constructor() {
//class => use this
        this.restrict ='E';
        this.replace= true;
        this.controller= CategoryController;
        this.controllerAs= 'categoryController';
        this.templateUrl= AngularApp.webDir + 'bundles/cpasimusantesimupoll/js/Category/Partials/category.directive.html';
        //this.scope= { paper: '=', exercise: '=', user: '=', currentStepIndex: '=' };
    }
    link(scope, element, attr, categoryCtrl) {
        //categoryCtrl.init(scope.paper, scope.exercise, scope.user, scope.currentStepIndex);
    }
}
