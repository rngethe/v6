<template>
  <div class="column-menu-main w-row" style="margin-top: 120px">
    <div class="w-row">
      <div class="col-md-2 p-1">
        <transition name="slide-right">
          <div class="col-md-12 mt-2">
            <div class="container-menu-funnel">
              <div v-for="(settingsCat, index) in settingsCategories[langue]" :key="index">
                <a @click="menuHighlight = index"
                   class="menu-item"
                   :class="menuHighlight == index ? 'w--current' : ''"
                >{{ settingsCat }}</a>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <div class="col-md-10 p-1" style="padding-left: 2em !important;">
        <h2>{{settingsCategories[langue][menuHighlight]}}</h2>
        <p class="paragraphe-sous-titre">{{funnelDescription[langue][menuHighlight]}}</p>
        <transition name="slide-right">
          <editHomepage
                  v-if="menuHighlight == 0"
                  ref="homepage"
                  :actualLanguage="actualLanguage"
          ></editHomepage>

          <editStatus
                  v-if="menuHighlight == 1"
                  ref="datas"
          ></editStatus>

          <editTags
                  v-if="menuHighlight == 2"
                  ref="tags"
          ></editTags>
        </transition>
      </div>
    </div>

    <div
            class="section-sauvegarder-et-continuer-funnel"
    >
      <div class="w-container">
        <div class="container-evaluation w-clearfix">
          <a @click="next()" class="bouton-sauvergarder-et-continuer-3">{{ Continuer }}</a>
          <a class="bouton-sauvergarder-et-continuer-3 w-retour" @click="previous()">
            {{
            Retour
            }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import editStatus from "../components/Settings/editStatus";
import editTags from "../components/Settings/editTags";
import editHomepage from "../components/Settings/editHomepage";

const qs = require("qs");

export default {
  name: "globalSettings",

  components: {
    editStatus,
    editTags,
    editHomepage
  },

  props: {
    actualLanguage: String
  },

  data: () => ({
    menuHighlight: 0,
    langue: 0,

    funnelDescription: [
      [
        Joomla.JText._("COM_EMUNDUS_ONBOARD_HOMEDESCRIPTION"),
        Joomla.JText._("COM_EMUNDUS_ONBOARD_STATUSDESCRIPTION"),
        Joomla.JText._("COM_EMUNDUS_ONBOARD_TAGSDESCRIPTION"),
      ],
      [
        Joomla.JText._("COM_EMUNDUS_ONBOARD_HOMEDESCRIPTION"),
        Joomla.JText._("COM_EMUNDUS_ONBOARD_STATUSDESCRIPTION"),
        Joomla.JText._("COM_EMUNDUS_ONBOARD_TAGSDESCRIPTION"),
      ]
    ],

    settingsCategories: [
      [
        "Page d'accueil",
        "Statuts",
        "Etiquettes"
      ],
      [
        "Home page",
        "Status",
        "Tags"
      ]
    ],

    Retour: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_RETOUR"),
    Continuer: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_CONTINUER"),
  }),

  methods: {
    next() {
      if (this.menuHighlight == 0) {
        this.menuHighlight++;
        this.updateHomepage(this.$refs.homepage.$data.form.content);
      } else if (this.menuHighlight == 1) {
        this.menuHighlight++;
        this.updateStatus(this.$refs.datas.$data.status);
      } else {
        this.updateTags(this.$refs.tags.$data.tags);
      }
    },

    updateStatus(status) {
      axios({
        method: "post",
        url: 'index.php?option=com_emundus_onboard&controller=settings&task=updatestatus',
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        data: qs.stringify({
          status: status
        })
      }).then(() => {});
    },

    updateTags(tags){
      axios({
        method: "post",
        url: 'index.php?option=com_emundus_onboard&controller=settings&task=updatetags',
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        data: qs.stringify({
          tags: tags
        })
      }).then(() => {
        window.location.replace("campaigns");
      });
    },

    updateHomepage(content) {
      axios({
        method: "post",
        url: 'index.php?option=com_emundus_onboard&controller=settings&task=updatehomepage',
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        data: qs.stringify({
          content: content
        })
      }).then(() => {});
    },

    previous() {
      if (this.menuHighlight > 0) {
        this.menuHighlight--;
      } else {
        history.go(-1);
      }
    },
  },

  created() {
    if (this.actualLanguage == "en") {
      this.langue = 1;
    }
  },
};
</script>

<style scoped>
</style>
