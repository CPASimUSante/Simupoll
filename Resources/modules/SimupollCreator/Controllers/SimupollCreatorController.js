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

        this.tinymceOptions.plugins         = plugins
        this.tinymceOptions.toolbar1        = toolbar
        this.tinymceOptions.trusted         = true
        this.tinymceOptions.format          = 'html'

        //modal variables
        this._addedSimupoll                 = {}
        this._deletedPropositionQuestion    = null
        this._proposition_index             = 0
        this.errors                         = []
        this.errorMessage                   = null
        this._modalFactory                  = simupollCreatorModal
        this._modalInstance                 = null
        this._service                       = SimupollCreatorService

        this.description                    = SimupollCreatorService.getDescription()
        this.categories                     = SimupollCreatorService.getTree()
        //questions / propositions
        this.simupolldata                   = SimupollCreatorService.getSimupoll()
    }

    saveSimupoll(form) {
        if (form.$valid) {
            this._service.saveSimupoll(
              this.description,
              this.simupolldata,
              () => this._modal(errorTemplate, 'simupoll_save_failure')
          )
        }
    }

    inlineAddQuestion() {
        this.doAddQuestion()
    }

    doAddQuestion() {
        this._service.addQuestion(
          this.simupolldata,
          () => this._modal(errorTemplate, 'question_add_failure')
        )
    }

    inlineAddProposition(question) {
        this.doAddPropostion(question)
    }

    doAddPropostion(question) {
        this._service.addProposition(
          this.simupolldata,
          question,
          () => this._modal(errorTemplate, 'proposition_add_failure')
        )
    }

    showDeleteQuestion(index) {
        this._question_index = index
        this._modal(deleteQuestionTemplate)
    }

    doDeleteQuestion(index) {
        this._service.deleteQuestion(
          this._question_index,
          () => this._modal(errorTemplate, 'question_delete_failure')
        )
        this._closeModal()
    }

    doAddProposition(form) {
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeleteProposition(question, index) {
        this._deletedPropositionQuestion = question
        this._proposition_index = index
        this._modal(deletePropositionTemplate)
    }

    doDeleteProposition(proposition) {
        this._service.deleteProposition(
          this._deletedPropositionQuestion,
          this._proposition_index,
          () => this._modal(errorTemplate, 'proposition_delete_failure')
        )
        this._closeModal()
    }

    //close X modal
    cancel (form) {
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
