export default class periodDirective {
    constructor(PeriodController) {
        //class => use this
        this.restrict       ='E'
        this.replace        = true
        this.controller     = PeriodController
        this.controllerAs   = 'PeriodController'
        this.templateUrl    = AngularApp.webDir + 'bundles/cpasimusantesimupoll/js/Period/Partials/period.directive.html'
        this.scope          = { periods: '@', sid: '='}
    }

    link(scope, element, attr, PeriodController) {
        PeriodController.init(scope.periods, scope.sid)
    }
}
