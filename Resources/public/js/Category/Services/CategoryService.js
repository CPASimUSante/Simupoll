export default class CategoryService {
//no import of Angular stuff ($http, $q…)
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
    }
    getCategories(sid) {
        return this.$http.get('/categories/'+sid)
    }
}
