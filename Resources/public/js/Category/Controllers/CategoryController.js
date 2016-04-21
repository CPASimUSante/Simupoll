export default class CategoryController {
//no import of Angular stuff ($window, $scope…)
    constructor($window, $scope, CategoryService) {
        //declaration of variables
        this.$window            = $window;
        this.$scope             = $scope;
        this.CategoryService    = CategoryService;
        this.tree               = {};
        this.sid                = 0;
    }
    
    init(tree, sid) {
        this.tree = tree;
        this.sid = sid;
    }
}
