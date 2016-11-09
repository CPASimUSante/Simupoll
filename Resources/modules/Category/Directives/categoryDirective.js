export default class categoryDirective {
    constructor(CategoryController, categoryTemplate) {
        //class => use this
        this.restrict           = 'E'
        this.replace            = true
        this.controller         = CategoryController
        this.bindToController   = true//{ tree: '@', sid: '='} //to avoid using $scope
        this.controllerAs       = 'vm'
        this.template           = categoryTemplate
    }
}
