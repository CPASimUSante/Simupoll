//no import of Angular stuff ($http, $q…)
export default class CategoryService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
    }
    
    getCategories(sid) {
        return this.$http.get('/categories/'+sid)
    }
}
