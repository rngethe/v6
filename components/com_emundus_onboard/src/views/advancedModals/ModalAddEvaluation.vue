<template>
  <!-- modalC -->
  <span :id="'modalAddEvaluation'">
    <modal
      :name="'modalAddEvaluation'"
      height="auto"
      transition="nice-modal-fade"
      :min-width="200"
      :min-height="200"
      :delay="100"
      :adaptive="true"
      :clickToClose="false"
      @closed="beforeClose"
      @before-open="beforeOpen"
    >
      <div class="modalC-content">
        <div class="update-field-header">
          <div class="topright">
            <button type="button" class="btnCloseModal" @click.prevent="$modal.hide('modalAddEvaluation')">
              <em class="fas fa-times-circle"></em>
            </button>
          </div>
          <h2 class="update-title-header">
             {{addGrid}}
          </h2>
        </div>
        <div class="form-group">
          <label>{{ChooseExistingGridModel}} :</label>
          <select v-model="model_id" class="dropdown-toggle">
            <option value="-1"></option>
            <option v-for="(model, index) in models" :value="model.form_id">{{model.label}}</option>
          </select>
        </div>
        <div class="form-group" :class="{ 'mb-0': translate.label}">
          <label>{{Name}}* :</label>
          <div class="input-can-translate">
            <input v-model="label.fr" type="text" maxlength="40" class="form__input field-general w-input" id="menu_label" style="margin: 0" :class="{ 'is-invalid': errors}"/>
            <button class="translate-icon" :class="{'translate-icon-selected': translate.label}" type="button" @click="translate.label = !translate.label"></button>
          </div>
        </div>
        <div class="inlineflex" v-if="translate.label">
          <label class="translate-label">
            {{TranslateEnglish}}
          </label>
          <i class="fas fa-sort-down"></i>
        </div>
        <div class="form-group mb-1" v-if="translate.label">
          <input v-model="label.en" type="text" maxlength="40" class="form__input field-general w-input"/>
        </div>
        <p v-if="errors && model_id == -1" class="error col-md-12 mb-2">
          <span class="error">{{LabelRequired}}</span>
        </p>
        <div class="form-group mt-1" :class="{'mb-0': translate.intro}">
          <label>{{Intro}} :</label>
          <div class="input-can-translate">
              <textarea v-model="intro.fr" class="form__input field-general w-input" rows="3" maxlength="300" style="margin: 0"></textarea>
              <button class="translate-icon" :class="{'translate-icon-selected': translate.intro}" type="button" @click="translate.intro = !translate.intro"></button>
          </div>
        </div>
        <div class="inlineflex" v-if="translate.intro">
          <label class="translate-label">
            {{TranslateEnglish}}
          </label>
          <em class="fas fa-sort-down"></em>
        </div>
        <div class="form-group mb-1" v-if="translate.intro">
          <textarea v-model="intro.en" rows="3" class="form__input field-general w-input" maxlength="300"></textarea>
        </div>
        <div class="col-md-12 d-flex" v-if="model_id == -1">
          <input type="checkbox" v-model="template">
          <label class="ml-10px">{{SaveAsTemplate}} :</label>
        </div>
      </div>
      <div class="col-md-12 mb-1">
        <a
                class="bouton-sauvergarder-et-continuer-3"
                @click.prevent="createGrid()"
        >{{ Continuer }}</a>
        <a
                class="bouton-sauvergarder-et-continuer-3 w-retour"
                @click.prevent="$modal.hide('modalAddEvaluation')"
        >{{Retour}}</a>
      </div>
      <div class="loading-form" style="top: 10vh" v-if="submitted">
        <Ring-Loader :color="'#de6339'" />
      </div>
    </modal>
  </span>
</template>

<script>
import axios from "axios";
import ModalEmailPreview from "./ModalEmailPreview";
const qs = require("qs");

export default {
  name: "modalAddEvaluation",
  components: {},
  props: { prog: Number, grid: Number },
  data() {
    return {
      translate: {
        label: false,
        intro: false
      },
      label: {
        fr: '',
        en: ''
      },
      intro: {
        fr: '',
        en: ''
      },
      model_id: -1,
      template: 0,
      models: [],
      errors: false,
      changes: false,
      submitted: false,
      addGrid: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDGRID"),
      ChooseExistingGridModel: Joomla.JText._("COM_EMUNDUS_ONBOARD_GRIDMODEL"),
      Name: Joomla.JText._("COM_EMUNDUS_ONBOARD_FIELD_NAME"),
      Intro: Joomla.JText._("COM_EMUNDUS_ONBOARD_FIELD_INTRO"),
      Retour: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_RETOUR"),
      Continuer: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_CONTINUER"),
      LabelRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORM_REQUIRED_NAME"),
      TranslateEnglish: Joomla.JText._("COM_EMUNDUS_ONBOARD_TRANSLATE_ENGLISH"),
      SaveAsTemplate: Joomla.JText._("COM_EMUNDUS_ONBOARD_SAVE_AS_TEMPLATE"),
    };
  },
  methods: {
    beforeClose(event) {},
    beforeOpen(event) {
      this.getExistingGrids();
    },
    getExistingGrids() {
      axios({
        method: "get",
        url: "index.php?option=com_emundus_onboard&controller=program&task=getgridsmodel"
      }).then(response => {
        this.models = response.data.data;
      });
    },
    createGrid() {
      this.changes = true;

      if(this.label.fr != '' || this.model_id != -1) {
        if(!this.translate.label){
          this.label.en = this.label.fr;
        }
        if(!this.translate.intro){
          this.intro.en = this.intro.fr;
        }
        this.submitted = true;
        axios({
          method: "post",
          url:
                  "index.php?option=com_emundus_onboard&controller=program&task=creategrid",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          data: qs.stringify({
            label: this.label,
            intro: this.intro,
            modelid: this.model_id,
            template: this.template,
            pid: this.prog
          })
        }).then((result) => {
          this.submitted = false;
          this.$emit("updateGrid");
          this.$modal.hide('modalAddEvaluation');
        }).catch(e => {
          console.log(e);
        });
      } else {
        this.errors = true;
      }
    }
  },

  watch: {
    model_id: function (value) {
      if(value != -1){
        this.models.forEach(model => {
          if(model.form_id == this.model_id){
            this.label.fr = model.label;
            this.intro.fr = model.intro;
          }
        });
      } else {
        this.label.fr = '';
        this.intro.fr = '';
      }
    },
  }
};
</script>

<style scoped>
.modalC-content {
  height: 100%;
  box-sizing: border-box;
  padding: 10px;
  font-size: 15px;
  overflow: auto;
}
.topright {
  font-size: 25px;
  float: right;
}
.btnCloseModal {
  background-color: inherit;
}
  .update-field-header{
    margin-bottom: 1em;
  }

  .update-title-header{
    margin-top: 0;
    display: flex;
    align-items: center;
  }

  .require{
    margin-bottom: 10px !important;
  }

.inputF{
  margin: 0 0 10px 0 !important;
}

  .d-flex{
    display: flex;
    align-items: center;
  }

  .dropdown-custom{
    height: 35px;
  }

  .users-block{
    height: 15em;
    overflow: scroll;
  }

.user-item{
  display: flex;
  padding: 10px;
  background-color: #f0f0f0;
  border-radius: 5px;
  align-items: center;
  margin-bottom: 1em;
}

.bigbox{
  height: 30px !important;
  width: 30px !important;
  cursor: pointer;
}

  .btnPreview{
    margin-bottom: 10px;
    position: relative;
    background: transparent;
  }

  .select-all{
    display: flex;
    align-items: end;
    margin-bottom: 1em;
  }
</style>
