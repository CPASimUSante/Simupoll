//the template for modals
import addQuestionTemplate from '../Partials/questions-list.html'
import addPropositionTemplate from '../Partials/propositions-list.html'
import deleteQuestionTemplate from '../Partials/modalDeleteQuestion.html'
import deletePropositionTemplate from '../Partials/modalDeleteProposition.html'
import errorTemplate from '../../Common/Partials/modalError.html'

export default class SimupollCreatorController {
    constructor(SimupollCreatorService, simupollCreatorModal) {
        // Initialize TinyMCE
        let tinymce               = window.tinymce
        tinymce.claroline.init    = tinymce.claroline.init || {}
        tinymce.claroline.plugins = tinymce.claroline.plugins || {}

        this.tinymceOptions = {}

        const plugins = [
            'autoresize advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars fullscreen',
            'insertdatetime media nonbreaking table directionality',
            'template paste textcolor emoticons code'
        ]
        let toolbar = 'undo redo | styleselect | bold italic underline | forecolor | alignleft aligncenter alignright | preview fullscreen'

        $.each(tinymce.claroline.plugins, function(key, value) {
            if ('autosave' != key &&  value === true) {
                plugins.push(key)
                toolbar += ' ' + key
            }
        })

        for (let prop in tinymce.claroline.configuration) {
            if (tinymce.claroline.configuration.hasOwnProperty(prop)) {
                this.tinymceOptions[prop] = tinymce.claroline.configuration[prop]
            }
        }

        this.tinymceOptions.plugins = plugins
        this.tinymceOptions.toolbar1 = toolbar
        this.tinymceOptions.trusted = true
        this.tinymceOptions.format = 'html'

        //modal variables
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = simupollCreatorModal
        this._modalInstance     = null
        this._service           = SimupollCreatorService

        //questions / propositions
        this.dd = 'ABC';
        let simupollquestions = [
            {'title':'title1','id':1,'propositions':[{'choice':5,'mark':2},{'choice':3,'mark':1}]},
            {'title':'title2','id':2,'propositions':[{'choice':2,'mark':3}]},
            {'title':'title3','id':3,'propositions':[{'choice':1,'mark':4},{'choice':2,'mark':5},{'choice':3,'mark':6}]},
        ]
        this.simupollquestions = simupollquestions
    }

    inlineAddQuestion() {
        console.log('inlineadd');
    }

    doAddQuestion() {

    }

    showDeleteQuestion(question) {
        this._modal(deleteQuestionTemplate)
    }

    doDeleteQuestion(question) {

    }

    doAddProposition(form) {
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeleteProposition(question) {
        this._modal(deletePropositionTemplate)
    }

    doDeleteProposition(question) {
        this._closeModal()
    }

    //close X modal
    cancel (form) {
console.log('close')
      if (form) {
        this._resetForm(form)
      }
      this._modalInstance.dismiss()
    }

    _modal (template, errorMessage, errors) {
      if (errorMessage) {
        this.errorMessage = errorMessage
      }
      if (errors) {
        this.errors = errors
      }
      this._modalInstance = this._modalFactory.open(template)
    }

    _closeModal () {
        this._modalInstance.close()
    }

    _resetForm (form) {
      this.errorMessage = null
      this.errors = []
      form.$setPristine()
      form.$setUntouched()
    }
}
