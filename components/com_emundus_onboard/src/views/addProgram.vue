<template>
  <div class="section-principale">
    <div class="w-container">
      <div class="sous-container">
        <p class="required">{{RequiredFieldsIndicate}}</p>
        <div class="heading-form">
          <div class="icon-title programme"></div>
          <h2 class="heading">{{ Program }}</h2>
        </div>
        <p class="paragraphe-sous-titre">
          {{ AddProgramDesc }}
        </p>
        <div class="w-form">
          <form id="program-form" @submit.prevent="submit">
            <div class="form-group prog-label">
              <label for="prog_label">{{ProgName}} *</label>
              <input
                id="prog_label"
                type="text"
                class="form__input field-general w-input"
                placeholder=" "
                v-model="form.label"
                v-focus
                @keyup="updateCode"
                :class="{ 'is-invalid': errors.label }"
              />
            </div>
            <p v-if="errors.label" class="error col-md-12 mb-2">
              <span class="error">{{LabelRequired}}</span>
            </p>

            <div class="form-group prog-code">
              <label for="prog_code" style="top: 12.8em">{{ProgCode}} *</label>
              <input
                id="prog_code"
                type="text"
                class="form__input field-general w-input"
                placeholder=" "
                v-model="form.code"
                @keyup="checkCode"
                :class="{ 'is-invalid': errors.code }"
              />
            </div>
            <p v-if="errors.code" class="error col-md-12 mb-2">
              <span class="error">{{CodeRequired}}</span>
            </p>

            <div class="form-group prog-label">
              <label for="prog_code" style="top: 10.7em">{{ChooseCategory}} *</label>
              <autocomplete
                @searched="onSearchCategory"
                :id="'prog_category'"
                :items="this.categories"
                :year="form.programmes"
              />
            </div>

            <div class="form-group controls">
              <editor :text="form.notes" :lang="actualLanguage" v-if="dynamicComponent" :id="'program'" v-model="form.notes"  :placeholder="ProgramResume"></editor>
            </div>

            <div class="form-group d-flex">
              <label for="published">{{ Publish }}</label>
              <div class="toggle">
                <input
                  type="checkbox"
                  true-value="1"
                  false-value="0"
                  class="check"
                  id="published"
                  name="published"
                  v-model="form.published"
                />
                <strong class="b switch"></strong>
                <strong class="b track"></strong>
              </div>
            </div>

            <div class="form-group d-flex last-container">
              <label for="apply">{{ DepotDeDossier }}</label>
              <div class="toggle">
                <input
                  type="checkbox"
                  true-value="1"
                  false-value="0"
                  class="check"
                  id="apply"
                  name="apply"
                  v-model="form.apply_online"
                />
                <strong class="b switch"></strong>
                <strong class="b track"></strong>
              </div>
            </div>
            <div class="section-sauvegarder-et-continuer">
              <div class="w-container">
                <div class="container-evaluation w-clearfix">
                  <button class="bouton-sauvergarder-et-continuer w-button"
                          type="button"
                          @click="quit = 1; submit()">
                    {{ Continuer }}
                  </button>
                  <button class="bouton-sauvergarder-et-continuer w-quitter w-button"
                          type="button"
                          @click="quit = 0; submit()">
                    {{ Quitter }}
                  </button>
                  <button
                    type="button"
                    class="bouton-sauvergarder-et-continuer w-retour w-button"
                    onclick="history.go(-1)"
                  >
                    {{ Retour }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="loading-form" v-if="submitted">
      <RingLoader :color="'#de6339'" />
    </div>
  </div>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import axios from "axios";
import Editor from "../components/editor";
import Autocomplete from "../components/autocomplete";

const qs = require("qs");

export default {
  name: "addProgram",

  components: {
    Editor,
    Autocomplete
  },

  directives: { focus: {
      inserted: function (el) {
        el.focus()
      }
    }
  },

  props: {
    prog: Number,
    actualLanguage: String
  },

  data: () => ({
    dynamicComponent: false,
    isHidden: false,

    new_category: "",
    categories: [],
    cats: [],
    programs: [],

    quit: 1,

    Program: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADDCAMP_PROGRAM"),
    AddProgram: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADDPROGRAM"),
    ChooseProg: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADDCAMP_CHOOSEPROG"),
    Retour: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_RETOUR"),
    Quitter: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_QUITTER"),
    Continuer: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_CONTINUER"),
    Publish: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_PUBLISH"),
    DepotDeDossier: Joomla.JText._("COM_EMUNDUS_ONBOARD_DEPOTDEDOSSIER"),
    ProgName: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGNAME"),
    ProgCode: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGCODE"),
    ChooseCategory: Joomla.JText._("COM_EMUNDUS_ONBOARD_CHOOSECATEGORY"),
    NameCategory: Joomla.JText._("COM_EMUNDUS_ONBOARD_NAMECATEGORY"),
    LabelRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORM_REQUIRED_NAME"),
    CodeRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROG_REQUIRED_CODE"),
    CategoryRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROG_REQUIRED_CATEGORY"),
    RequiredFieldsIndicate: Joomla.JText._("COM_EMUNDUS_ONBOARD_REQUIRED_FIELDS_INDICATE"),
    ProgramResume: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGRAM_RESUME"),
    AdvancedSettings: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGRAM_ADVANCED_SETTINGS"),

    form: {
      label: "",
      code: "",
      programmes: "",
      notes: "",
      synthesis:
        '<ul><li><strong>[APPLICANT_NAME]</strong></li><li><a href="mailto:[EMAIL]">[EMAIL]</a></li></ul>',
      tmpl_trombinoscope:
        '<table cellpadding="2" style="width: 100%;"><tbody><tr style="border-collapse: collapse;"><td align="center" valign="top" style="text-align: center;"><p style="text-align: center;"><img src="[PHOTO]" alt="Photo" height="100" /> </p><p style="text-align: center;"><b>[NAME]</b><br /></p></td></tr></tbody></table>',
      tmpl_badge:
        '<table width="100%"><tbody><tr><td style="vertical-align: top; width: 100px;" align="left" valign="middle" width="30%"><img src="[LOGO]" alt="Logo" height="50" /></td><td style="vertical-align: top;" align="left" valign="top" width="70%"><b>[NAME]</b></td></tr></tbody></table>\n',
      published: 1,
      apply_online: 1
    },
    submitted: false,
    errors: {
      label: false,
      code: false,
      category: false
    }
  }),

  validations: {
    form: {
      label: { required },
      code: { required },
    }
  },

  methods: {
    submit() {
      this.errors.label = false;
      this.errors.code = false;
      this.errors.category = false;

      if(this.form.label == ""){
        this.errors.label = true;
        return 0;
      }

      if(this.form.code == ""){
        this.errors.code = true;
        return 0;
      }

      // Check if we are creating a new category and set the form category element
      if (this.new_category !== "") {
        this.form.programmes = this.new_category;
      }

      // stop here if form is invalid
      this.$v.$touch();

      if (this.$v.$invalid) {
        return;
      }
      this.submitted = true;
      if (this.prog !== "") {
        axios({
          method: "post",
          url: "index.php?option=com_emundus_onboard&controller=program&task=updateprogram",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          data: qs.stringify({ body: this.form, code: this.prog })
        })
          .then(response => {
            this.quitFunnelOrContinue(this.quit);
          })
          .catch(error => {
            console.log(error);
          });
      } else {
        axios({
          method: "post",
          url: "index.php?option=com_emundus_onboard&controller=program&task=createprogram",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          data: qs.stringify({ body: this.form })
        })
          .then(response => {
            this.prog = response.data.data;
            this.quitFunnelOrContinue(this.quit);
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    onSearchCategory(value) {
      this.form.programmes = value;
    },
    updateCode() {
      if(this.form.label !== ''){
        this.form.code = this.form.label.toUpperCase().replace(/[^a-zA-Z0-9]/g,'_').substring(0,10) + '_00';
        this.programs.forEach((element, index) => {
          if(this.form.code == element.code){
            let newCode = parseInt(element.code.split('_')[1]) + 1;
            if(newCode > 10) {
              this.form.code = this.form.label.toUpperCase() + '_' + newCode;
            } else {
              this.form.code = this.form.label.toUpperCase() + '_0' + newCode;
            }
          }
        });
      } else {
        this.form.code = '';
      }
    },

    checkCode() {
      this.programs.forEach((element, index) => {
        if(this.form.code.toUpperCase() == element.code){
          let newCode = parseInt(element.code.split('_')[1]) + 1;
          if(newCode > 10) {
            this.form.code = this.form.label.toUpperCase()  + '_' + newCode;
          } else {
            this.form.code = this.form.label.toUpperCase()  + '_0' + newCode;
          }
        }
      });
    },

    quitFunnelOrContinue(quit) {
      if (quit == 0) {
        history.go(-1);
      }
      else if (quit == 1) {
        window.location.replace('index.php?option=com_emundus_onboard&view=program&layout=advancedsettings&pid=' + this.prog);
      }
    },
  },

  created() {
    axios
      .get("index.php?option=com_emundus_onboard&controller=program&task=getprogramcategories")
      .then(response => {
        this.categories = response.data.data;
        for (var i = 0; i < this.categories.length; i++) {
          this.cats.push(this.categories[i]);
        }
        if (this.prog !== "") {
          axios
            .get(
              `index.php?option=com_emundus_onboard&controller=program&task=getprogrambyid&id=${this.prog}`
            ).then(rep => {
              this.form.code = rep.data.data.code;
              this.form.label = rep.data.data.label;
              this.form.notes = rep.data.data.notes;
              this.form.programmes = rep.data.data.programmes;
              this.form.tmpl_badge = rep.data.data.tmpl_badge;
              this.form.published = rep.data.data.published;
              this.form.apply_online = rep.data.data.apply_online;
              if (rep.data.data.synthesis != null) {
                this.form.synthesis = rep.data.data.synthesis.replace(/>\s+</g, "><");
              }
              if (rep.data.data.tmpl_trombinoscope != null) {
                this.form.tmpl_trombinoscope = rep.data.data.tmpl_trombinoscope.replace(
                  />\s+</g,
                  "><"
                );
              }
              this.dynamicComponent = true;
            }).catch(e => {
              console.log(e);
            });
        } else {
          this.dynamicComponent = true;
        }
      })
      .catch(e => {
        console.log(e);
      });

    axios
            .get("index.php?option=com_emundus_onboard&controller=program&task=getallprogram")
            .then(response => {
              this.programs = response.data.data;
              this.programs.sort((a, b) => a.id - b.id);
            })
            .catch(e => {
              console.log(e);
            });
  }
};
</script>

<style scoped>
.is-invalid {
  border-color: #dc3545 !important;
}
.is-invalid:hover,
.is-invalid:focus {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

input[type="radio"],
input[type="checkbox"] {
  width: 100% !important;
}

.toggle > b {
  display: block;
}

.toggle {
  position: relative;
  width: 40px;
  height: 20px;
  border-radius: 100px;
  background-color: #ddd;
  overflow: hidden;
  box-shadow: inset 0 0 2px 1px rgba(0, 0, 0, 0.05);
}

.check {
  position: absolute;
  display: block;
  cursor: pointer;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  z-index: 6;
}

.check:checked ~ .track {
  box-shadow: inset 0 0 0 20px #4bd863;
}

.check:checked ~ .switch {
  right: 2px;
  left: 22px;
  transition: 0.35s cubic-bezier(0.785, 0.135, 0.15, 0.86);
  transition-property: left, right;
  transition-delay: 0.05s, 0s;
}

.switch {
  position: absolute;
  left: 2px;
  top: 2px;
  bottom: 2px;
  right: 22px;
  background-color: #fff;
  border-radius: 36px;
  z-index: 1;
  transition: 0.35s cubic-bezier(0.785, 0.135, 0.15, 0.86);
  transition-property: left, right;
  transition-delay: 0s, 0.05s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.track {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  transition: 0.35s cubic-bezier(0.785, 0.135, 0.15, 0.86);
  box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.05);
  border-radius: 40px;
}

.plus.w-inline-block {
  background-color: white;
  border-color: #cccccc;
}

.w-input,
.w-select {
  min-height: 55px;
  padding: 12px;
  background-color: white !important;
}

.bouton-sauvergarder-et-continuer {
  position: relative;
  padding: 10px 30px;
  float: right;
  border-radius: 4px;
  background-color: #1b1f3c;
  -webkit-transition: background-color 200ms cubic-bezier(0.55, 0.085, 0.68, 0.53);
  transition: background-color 200ms cubic-bezier(0.55, 0.085, 0.68, 0.53);
}

.w-quitter {
  margin-right: 5%;
  background: none !important;
  border: 1px solid #1b1f3c;
  color: #1b1f3c;
}

.last-container {
  padding-bottom: 30px;
}

.section-principale {
  padding-bottom: 0;
}

h2 {
  color: #1b1f3c;
}

.d-flex{
  display: flex;
  align-items: center;
}

.d-flex label{
  margin-bottom: 0;
  margin-right: 10px;
}
</style>
