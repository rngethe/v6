<template>
  <!-- modalC -->
  <span :id="'modalAddDocuments'">
    <modal
            :name="'modalAddDocuments'"
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
            <button type="button" class="btnCloseModal" @click.prevent="$modal.hide('modalAddDocuments')">
              <em class="fas fa-times-circle"></em>
            </button>
          </div>
          <h2 class="update-title-header">
             {{createDocument}}
          </h2>
        </div>
        <div class="form-group">
          <label for="name">{{Name}}* :</label>
          <input type="text" maxlength="40" class="form__input field-general w-input" v-model="form.name" id="name" :class="{ 'is-invalid': errors.name}" />
          <p v-if="errors.name" class="error col-md-12 mb-2">
            <span class="error">{{NameRequired}}</span>
          </p>
        </div>
        <div class="form-group">
          <label for="description">{{Description}} :</label>
          <textarea type="text" maxlength="200" class="form__input field-general w-input" v-model="form.description" id="description" />
        </div>
        <div class="form-group">
          <label for="nbmax">{{MaxPerUser}}* :</label>
          <input type="number" max="100" min="1" class="form__input field-general w-input" v-model="form.nbmax" id="nbmax" :class="{ 'is-invalid': errors.nbmax}" />
          <p v-if="errors.nbmax" class="error col-md-12 mb-2">
            <span class="error">{{MaxRequired}}</span>
          </p>
        </div>
        <div class="form-group">
          <label for="nbmax" :class="{ 'is-invalid': errors.selectedTypes}">{{FileType}}* :</label>
          <div class="users-block" :class="{ 'is-invalid': errors.selectedUsers}">
            <div v-for="(type, index) in types" :key="index" class="user-item">
              <input type="checkbox" class="form-check-input bigbox" v-model="form.selectedTypes[type.value]">
              <div class="ml-10px">
                  <p>{{type.title}}</p>
              </div>
            </div>
          </div>
          <p v-if="errors.selectedTypes" class="error col-md-12 mb-2">
            <span class="error">{{TypeRequired}}</span>
          </p>
        </div>
      </div>
      <div class="col-md-12 mb-1">
        <a class="bouton-sauvergarder-et-continuer-3"
           @click.prevent="createNewDocument()">
          {{ Continuer }}
        </a>
        <a class="bouton-sauvergarder-et-continuer-3 w-retour"
           @click.prevent="$modal.hide('modalAddDocuments')">
          {{Retour}}
        </a>
      </div>
    </modal>
  </span>
</template>

<script>
  import axios from "axios";
  const qs = require("qs");

  export default {
    name: "modalAddDocuments",
    props: { cid: Number },
    data() {
      return {
        form: {
          name: '',
          description: '',
          nbmax: 1,
          selectedTypes: {
            pdf: false,
            'jpg;png;gif': false,
            'doc;docx;odt;xls;xlsx;odf': false
          },
        },
        errors: {
          name: false,
          nbmax: false,
          selectedTypes: false
        },
        types: [
          {
            title: 'Documents PDF',
            value: 'pdf'
          },
          {
            title: 'Images',
            value: 'jpg;png;gif'
          },
          {
            title: 'Documents Office',
            value: 'doc;docx;odt;xls;xlsx;odf'
          },
        ],
        selectedTypes: [],
        createDocument: Joomla.JText._("COM_EMUNDUS_ONBOARD_CREATE_DOCUMENT"),
        Retour: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_RETOUR"),
        Continuer: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
        Name: Joomla.JText._("COM_EMUNDUS_ONBOARD_LASTNAME"),
        Description: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADDCAMP_DESCRIPTION"),
        MaxPerUser: Joomla.JText._("COM_EMUNDUS_ONBOARD_MAXPERUSER"),
        FileType: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILETYPE_ACCEPTED"),
        NameRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROG_REQUIRED_LABEL"),
        MaxRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_MAXPERUSER_REQUIRED"),
        TypeRequired: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILETYPE_ACCEPTED_REQUIRED"),
      };
    },
    methods: {
      beforeClose(event) {
        this.form = {
          name: '',
        };
      },
      beforeOpen(event) {
      },
      createNewDocument() {
        this.errors = {
          name: false,
          nbmax: false,
          selectedTypes: false
        };
        if(this.form.name === ''){
          this.errors.name = true;
          return 0;
        }
        if(this.form.nbmax === '' || this.form.nbmax === 0){
          this.errors.nbmax = true;
          return 0;
        }
        if(Object.values(this.form.selectedTypes).every((val, i) => val === false )){
          this.errors.selectedTypes = true;
          return 0;
        }

        let types = [];

        Object.keys(this.form.selectedTypes).forEach(key => {
          if(this.form.selectedTypes[key] == true){
            types.push(key);
          }
        });

        axios({
          method: "post",
          url: 'index.php?option=com_emundus_onboard&controller=campaign&task=createdocument',
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          data: qs.stringify({
            document: this.form,
            types: types,
            cid: this.cid
          })
        }).then((rep) => {
          this.$emit("UpdateDocuments");
          this.$modal.hide('modalAddDocuments')
        });
      },
    },
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
