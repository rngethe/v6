import Vue from "vue";
import Vuelidate from "vuelidate";
import List from "./views/list.vue";
import AddProgram from "./views/addProgram.vue";
import AddProgramAdvancedSettings from "./views/addProgramAdvancedSettings.vue";
import AddCampaign from "./views/addCampaign.vue";
import AddEmail from "./views/addEmail.vue";
import AddForm from "./views/addForm.vue";
import AddFormNextCampaign from "./views/addFormNextCampaign.vue";
import GlobalSettings from "./views/globalSettings.vue";
import formBuilder from "./views/formBuilder.vue";
import VueJsModal from "vue-js-modal";
import { VTooltip, VPopover, VClosePopover } from 'v-tooltip';
import * as VueSpinnersCss from "vue-spinners-css";

Vue.directive('tooltip', VTooltip)
Vue.directive('close-popover', VClosePopover)
Vue.component('v-popover', VPopover)

import Notifications from "vue-notification";
import velocity from "velocity-animate";

Vue.use(Notifications, { velocity });
Vue.use(Vuelidate);
Vue.use(VueJsModal);
Vue.use(VueSpinnersCss);

Vue.config.productionTip = false;
Vue.config.devtools = true;

if (document.getElementById("em-list-vue")) {
  new Vue({
    el: "list",
    render(h) {
      return h(List, {
        props: {
          type: this.$el.attributes.type.value
        }
      });
    }
  });
}

if (document.getElementById("em-addProgram-vue")) {
  new Vue({
    el: "#em-addProgram-vue",
    render(h) {
      return h(AddProgram, {
        props: {
          prog: this.$el.attributes.prog.value,
          actualLanguage: this.$el.attributes.actualLanguage.value
        }
      });
    }
  });
}

if (document.getElementById("em-addProgramAdvancedSettings-vue")) {
  new Vue({
    el: "#em-addProgramAdvancedSettings-vue",
    render(h) {
      return h(AddProgramAdvancedSettings, {
        props: {
          prog: this.$el.attributes.prog.value,
          actualLanguage: this.$el.attributes.actualLanguage.value
        }
      });
    }
  });
}

if (document.getElementById("em-addCampaign-vue")) {
  new Vue({
    el: "#em-addCampaign-vue",
    render(h) {
      return h(AddCampaign, {
        props: {
          campaign: this.$el.attributes.campaign.value,
          actualLanguage: this.$el.attributes.actualLanguage.value
        }
      });
    }
  });
}

if (document.getElementById("em-addEmail-vue")) {
  new Vue({
    el: "#em-addEmail-vue",
    render(h) {
      return h(AddEmail, {
        props: {
          email: this.$el.attributes.email.value,
          actualLanguage: this.$el.attributes.actualLanguage.value
        }
      });
    }
  });
}

if (document.getElementById("em-addForm-vue")) {
  new Vue({
    el: "#em-addForm-vue",
    render(h) {
      return h(AddForm, {
        props: {
          profileId: this.$el.attributes.profileId.value,
          campaignId: this.$el.attributes.campaignId.value,
        }
      });
    }
  });
}

if (document.getElementById("em-addFormNextCampaign-vue")) {
  new Vue({
    el: "#em-addFormNextCampaign-vue",
    render(h) {
      return h(AddFormNextCampaign, {
        props: {
          campaignId: this.$el.attributes.campaignId.value,
          actualLanguage: this.$el.attributes.actualLanguage.value,
          index: this.$el.attributes.index.value,
        }
      });
    }
  });
}

if (document.getElementById("em-formBuilder-vue")) {
  new Vue({
    el: "#em-formBuilder-vue",
    render(h) {
      return h(formBuilder, {
        props: {
          prid: this.$el.attributes.prid.value,
          index: this.$el.attributes.index.value,
          cid: this.$el.attributes.cid.value,
        }
      });
    }
  });
}

if (document.getElementById("em-globalSettings-vue")) {
  new Vue({
    el: "#em-globalSettings-vue",
    render(h) {
      return h(GlobalSettings, {
        props: {
          actualLanguage: this.$el.attributes.actualLanguage.value
        }
      });
    }
  });
}
