<template>
  <div id="fieldF">
    <div class="row rowmodal">
      <div class="form-group">
        <label>{{fieldtype}} :</label>
        <select v-model="element.params.password" class="dropdown-toggle">
          <option value="0">{{textfield}}</option>
          <option value="2">{{phonefield}}</option>
          <option value="3">{{emailfield}}</option>
          <option value="6">{{numberfield}}</option>
        </select>
      </div>
      <div class="form-group">
        <label>{{placeholdertext}}
          <button v-tooltip="{
          content: msg,
          trigger: 'hover',
          placement: 'top-center',
          offset: 5,
          popperOptions: {
            preventOverflow: false
          },
          delay: {
            show: 500,
            hide: 300,
            },
          }"><em class="fas fa-question-circle"></em></button> :</label>
        <input type="text" class="form__input field-general w-input" v-model="element.params.placeholder" />
      </div>
      <div class="form-group">
        <label>{{helptext}} :</label>
        <input type="text" class="form__input field-general w-input" v-model="element.params.rollover" />
      </div>
      <div class="form-group">
        <label>{{maxlength}} :</label>
        <input type="number" max="255" min="1" class="form__input field-general w-input" v-model="element.params.maxlength" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "fieldF",
  props: { element: Object },
  data() {
    return {
      msg: '',
      path: window.location.protocol + '//' + window.location.host + '/media/com_emundus_onboard/',
      helptext: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_HELPTEXT"),
      placeholdertext: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_PLACEHOLDER"),
      sizetext: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_SIZE"),
      maxlength: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_MAXLENGTH"),
      fieldtype: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_FIELDTYPE"),
      textfield: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_FIELDTEXT"),
      phonefield: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_FIELDPHONE"),
      emailfield: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_EMAIL"),
      numberfield: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_NUMBER"),
      placeholderHelp: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_PLACEHOLDER_HELP"),
    };
  },
  methods: {},
  created(){
    if(window.location.host == 'localhost:8888'){
      this.path = window.location.protocol + '//' + window.location.host + '/v6/media/com_emundus_onboard/'
    }
    this.msg =
            '<p style="color: white">' + this.placeholderHelp + '</p>' +
            '<img src="' + this.path + 'placeholder.gif" />'

    this.element.params.maxlength = 255;
    this.element.params.password = 0;
  }
};
</script>
<style scoped>
.flex {
  display: flex;
  align-items: center;
  margin-bottom: 1em;
  height: 30px;
}
.rowmodal {
  margin-top: 0.5em;
  margin-bottom: 0.5em;
}
  button{
    background: transparent;
  }
#fieldF{
  padding: 10px;
}
</style>
