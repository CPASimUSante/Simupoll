export default class CategoryService {
//no import of Angular stuff ($window, $scope…)
    constructor($http, $filter, $q, $window, $scope) {
        //declaration of variables
        this.$http      = $http;
        this.$filter    = $filter;
        this.$window    = $window;
        this.$q         = $q;
        this.$scope     = $scope;
    }

}
