export default class categoryDirective {
    constructor(CategoryController) {
        //class => use this
        this.restrict           = 'E'
        this.replace            = true
        this.controller         = CategoryController
        this.bindToController   = true//{ tree: '@', sid: '='} //to avoid using $scope
        this.controllerAs       = 'vm'
        this.templateUrl        = '../../../../../bundles/cpasimusantesimupoll/js/Category/Partials/category.directive.html'
    }
}
