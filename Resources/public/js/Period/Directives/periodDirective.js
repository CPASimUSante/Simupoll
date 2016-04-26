export default class periodDirective {
    constructor(PeriodController) {
        //class => use this
        this.restrict           = 'E'
        this.replace            = true
        this.controller         = PeriodController
        this.bindToController   = true//{ tree: '@', sid: '='} //to avoid using $scope
        this.controllerAs       = 'vm'
        this.templateUrl        = '../../../../../bundles/cpasimusantesimupoll/js/Period/Partials/period.directive.html'
    }
}
