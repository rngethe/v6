<template>
  <div class="container-fluid">
    <notifications
            group="foo-velocity"
            position="top right"
            animation-type="velocity"
            :speed="500"
    />
    <ModalAffectCampaign
            :prid="prid"
    />
    <ModalMenu
            :profileId="prid"
            @AddMenu="pushMenu"
    />
    <ModalSide
            v-for="(value, index) in formObjectArray"
            :key="index"
            v-show="formObjectArray[indexHighlight]"
            :ID="value.rgt"
            :element="value.object"
            :menus="formObjectArray"
            :index="index"
            @show="show"
            @UpdateUx="UpdateUXT"
            @UpdateName="UpdateName"
            @UpdateVue="updateFormObjectAndComponent"
            @removeMenu="removeMenu"
    />
    <div class="row form-builder">
      <div class="heading-block col-md-offset-4">
        <h1 class="form-title" style="padding: 0; margin: 0">{{profileLabel}}</h1>
        <a :href="'index.php?option=com_emundus_onboard&view=form&layout=add&pid=' + this.prid" style="margin-left: 1em">
          <em class="fas fa-pencil-alt" data-toggle="tooltip" data-placement="top"></em>
        </a>
      </div>
      <div class="actions-menu menu-block">
        <div>
          <div class="heading-actions">
            <label class="form-title" style="padding: 0; margin: 0">{{Actions}}</label>
          </div>
          <div class="action-links">
              <a class="d-flex action-link" style="padding-top: 2em" @click="$modal.show('modalMenu')">
                <em class="add-page-icon col-md-offset-1"></em>
                <label class="action-label col-md-offset-2">{{addMenu}}</label>
              </a>
              <a class="d-flex action-link" @click="createGroup()">
                <em class="add-group-icon col-md-offset-1"></em>
                <label class="action-label col-md-offset-2">{{addGroup}}</label>
              </a>
              <a class="d-flex action-link" :class="{ 'disable-element': elementDisabled}" @click="showElements">
                <em class="add-element-icon col-md-offset-1"></em>
                <label class="action-label col-md-offset-2" :class="[{'disable-element': elementDisabled}, addingElement ? 'down-arrow' : 'right-arrow']">{{addItem}}</label>
              </a>
              <draggable
                      v-model="plugins"
                      v-bind="dragOptions"
                      v-if="addingElement"
                      handle=".handle"
                      @start="dragging = true;draggingIndex = index"
                      @end="addingNewElement($event)"
                      drag-class="plugin-drag"
                      chosen-class="plugin-chosen"
                      ghost-class="plugin-ghost"
                      style="padding-bottom: 2em">
                  <div class="d-flex plugin-link col-md-offset-3 handle" v-for="(plugin,index) in plugins" :id="'plugin_' + plugin.value">
                    <em :class="plugin.icon"></em>
                    <span class="ml-10px">{{plugin.name}}</span>
                  </div>
              </draggable>
          </div>
        </div>
        <a class="send-form-button" @click="sendForm">
          <label style="cursor: pointer">{{sendFormButton}}</label>
          <em class="fas fa-paper-plane" style="font-size: 20px"></em>
        </a>
      </div>
      <div class="col-md-8 col-md-offset-4 menu-block">
        <ul class="menus-row">
          <draggable
                  handle=".handle"
                  v-model="formObjectArray"
                  :class="'draggables-list'"
                  @end="SomethingChange"
          >
            <li v-for="(value, index) in formObjectArray" :key="index" class="MenuForm" @mouseover="enableGrab(index)" @mouseleave="disableGrab()">
              <span class="icon-handle" v-show="grab && indexGrab == index">
                <em class="fas fa-grip-vertical handle"></em>
              </span>
              <a @click="changeGroup(index,value.rgt)"
                 class="MenuFormItem"
                 :class="indexHighlight == index ? 'MenuFormItem_current' : ''">
                {{value.object.show_title.value}}
              </a>
            </li>
          </draggable>
        </ul>
        <div class="col-md-12 form-viewer-builder">
          <Builder
                  :object="formObjectArray[indexHighlight]"
                  v-if="formObjectArray[indexHighlight]"
                  :UpdateUx="UpdateUx"
                  @show="show"
                  @UpdateFormBuilder="updateFormObjectAndComponent"
                  @removeGroup="removeGroup"
                  :key="builderKey"
                  :rgt="rgt"
                  ref="builder"
          />
        </div>
      </div>
    </div>
    <div class="loading-form" v-if="loading">
      <Ring-Loader :color="'#de6339'" />
    </div>
  </div>
</template>


<script>
  import axios from "axios";

  import "@fortawesome/fontawesome-free/css/all.css";
  import "@fortawesome/fontawesome-free/js/all.js";

  import "../assets/css/formbuilder.css";
  import draggable from "vuedraggable";

  import Builder from "../components/formClean/Builder";
  import ModalSide from "../components/formClean/ModalSide";
  import ModalMenu from "../components/formClean/ModalMenu";

  import _ from 'lodash';
  import ModalElement from "../components/formClean/ModalElement";
  import ModalAffectCampaign from "../components/formClean/ModalAffectCampaign";

  const qs = require("qs");

  export default {
    name: "FormBuilder",
    props: {
      prid: String,
      index: Number,
      cid: Number
    },
    components: {
      ModalAffectCampaign,
      ModalElement,
      Builder,
      ModalSide,
      ModalMenu,
      draggable
    },
    data() {
      return {
        UpdateUx: false,
        showModal: false,
        indexHighlight: "0",
        indexGrab: "0",
        formObjectArray: [],
        thevalue: "",
        formList: "",
        profileLabel: "",
        id: 0,
        grab: 0,
        rgt: 0,
        builderKey: 0,
        animation: {
          enter: {
            opacity: [1, 0],
            translateX: [0, -300],
            scale: [1, 0.2]
          },
          leave: {
            opacity: 0,
            height: 0
          }
        },
        loading: false,
        link: '',
        dragging: false,
        draggingIndex: -1,
        elementDisabled: false,
        addingElement: false,
        plugins: {
          field: {
            id: 0,
            value: 'field',
            icon: 'fas fa-font',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_FIELD")
          },
          birthday: {
            id: 1,
            value: 'birthday',
            icon: 'far fa-calendar-alt',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_BIRTHDAY")
          },
          checkbox: {
            id: 2,
            value: 'checkbox',
            icon: 'far fa-check-square',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_CHECKBOX")
          },
          dropdown: {
            id: 3,
            value: 'dropdown',
            icon: 'fas fa-th-list',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_DROPDOWN")
          },
          radiobutton: {
            id: 4,
            value: 'radiobutton',
            icon: 'fas fa-list-ul',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_RADIOBUTTON")
          },
          textarea: {
            id: 5,
            value: 'textarea',
            icon: 'far fa-square',
            name: Joomla.JText._("COM_EMUNDUS_ONBOARD_TYPE_TEXTAREA")
          },
        },
        buildmenu: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDMENU"),
        preview: Joomla.JText._("COM_EMUNDUS_ONBOARD_PREVIEW"),
        addMenu: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDMENU"),
        addGroup: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDGROUP"),
        addItem: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDITEM"),
        Actions: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTIONS"),
        sendFormButton: Joomla.JText._("COM_EMUNDUS_ONBOARD_SEND_FORM"),
      };
    },
    methods: {
      createElement(gid,plugin,order) {
        if(!_.isEmpty(this.formObjectArray[this.indexHighlight].object.Groups)){
          this.loading = true;
          axios({
            method: "post",
            url:
                    "index.php?option=com_emundus_onboard&controller=formbuilder&task=createsimpleelement",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            data: qs.stringify({
              gid: gid,
              plugin: plugin
            })
          }).then((result) => {
            axios({
              method: "get",
              url: "index.php?option=com_emundus_onboard&controller=formbuilder&task=getElement",
              params: {
                element: result.data.scalar,
                gid: gid
              },
              paramsSerializer: params => {
                return qs.stringify(params);
              }
            }).then(response => {
              this.formObjectArray[this.indexHighlight].object.Groups['group_'+gid].elements['element'+response.data.id] = response.data;
              this.formObjectArray[this.indexHighlight].object.Groups['group_'+gid].elts.splice(order,0,response.data);
              this.$refs.builder.updateOrder(gid,this.formObjectArray[this.indexHighlight].object.Groups['group_'+gid].elts);
              this.loading = false;
            });
          });
        }
      },
      addingNewElement: function(evt) {
        this.dragging = false;
        this.draggingIndex = -1;
        let plugin = evt.clone.id.split('_')[1];
        let gid = evt.to.parentElement.parentElement.parentElement.id.split('_')[1];
        if(typeof gid != 'undefined'){
          this.createElement(gid,plugin,evt.newIndex)
        }
      },
      createGroup() {
        this.loading = true;
        axios({
          method: "post",
          url: "index.php?option=com_emundus_onboard&controller=formbuilder&task=createsimplegroup",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          data: qs.stringify({
            fid: this.formObjectArray[this.indexHighlight].object.id
          })
        }).then((result) => {
          axios({
            method: "post",
            url: "index.php?option=com_emundus_onboard&controller=formbuilder&task=getJTEXT",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            data: qs.stringify({
              toJTEXT: result.data.group_tag
            })
          }).then((resultTrad) => {
            result.data.group_showLegend = resultTrad.data;
            this.loading = false;
            this.pushGroup(result.data);
          });
        });
      },
      // Update component dynamically
      UpdateName(index, label) {
        this.formObjectArray[index].object.show_title.value = label;
      },
      UpdateUXT() {
        this.UpdateUx = true;
      },
      pushGroup(group) {
        this.formObjectArray.forEach((form, index) => {
          if(form.object.id == group.formid){
            this.formObjectArray[index]['object']['Groups']['group_'+group.group_id] = {
              elements: {},
              group_id: group.group_id,
              group_showLegend: group.group_showLegend,
              label_fr: group.label_fr,
              label_en: group.label_en,
              group_tag: group.group_tag,
              ordering: group.ordering
            };
          }
        });
        this.builderKey += 1;
        this.elementDisabled = false;
        setTimeout(() => {
          window.scrollTo(0,document.body.scrollHeight);
        }, 200);
      },
      pushMenu(menu){
        let menulist = {
          link: menu.link,
          rgt: menu.rgt
        }
        this.formList.push(menulist);
        axios.get("index.php?option=com_emundus_onboard&view=form&formid=" + menu.id + "&format=vue_jsonclean")
                .then(response => {
                  this.formObjectArray.push({
                    object: response.data,
                    rgt: menu.rgt,
                    link: menu.link
                  });
                  this.indexHighlight = this.formObjectArray.length - 1;
                })
      },
      removeMenu(form_id) {
        this.formObjectArray.forEach((form, index) => {
          if(form.object.id == form_id){
            this.formObjectArray.splice(index,1);
          }
        });
        this.builderKey += 1;
        this.indexHighlight -= 1;
      },
      removeGroup(group_id, form_id) {
        this.formObjectArray.forEach((form, index) => {
          if(form.object.id == form_id){
            delete this.formObjectArray[index]['object']['Groups']['group_'+group_id];
          }
        });
        this.builderKey += 1;
      },
      updateFormObjectAndComponent(){
        this.formObjectArray = [];
        this.getDataObject();
        this.builderKey += 1;
      },
      getElement(element,gid){
        axios({
          method: "get",
          url: "index.php?option=com_emundus_onboard&controller=formbuilder&task=getElement",
          params: {
            element: element,
            gid: gid
          },
          paramsSerializer: params => {
            return qs.stringify(params);
          }
        }).then(response => {
          this.formObjectArray[this.indexHighlight].object.Groups['group_'+gid].elements['element'+response.data.id] = response.data;
          this.formObjectArray[this.indexHighlight].object.Groups['group_'+gid].elts.push(response.data);
          this.builderKey += 1;
        });
      },
      //

      /**
       * ** Methods for notify
       */
      show(group, type = "", text = "", title = "Information") {
        this.$notify({
          group,
          title: `${title}`,
          text,
          type
        });
      },
      clean(group) {
        this.$notify({ group, clean: true });
      },

      //TODOS a refaire
      getDataObject() {
        this.indexHighlight = this.index;
        this.formList.forEach(element => {
          let ellink = element.link.replace("fabrik","emundus_onboard");
          axios
                  .get(ellink + "&format=vue_jsonclean")
                  .then(response => {
                    this.formObjectArray.push({
                      object: response.data,
                      rgt: element.rgt,
                      link: element.link
                    });
                  })
                  .then(r => {
                    this.formObjectArray.sort((a, b) => a.rgt - b.rgt);
                    this.rgt = this.formObjectArray[0].rgt;
                    this.loading = false;
                    this.elementDisabled = _.isEmpty(this.formObjectArray[this.indexHighlight].object.Groups);
                  })
                  .catch(e => {
                    console.log(e);
                  });
        });
      },

      /**
       *  ** Récupère toute les formes du profile ID
       */
      getForms(profile_id) {
        this.loading = true;
        axios({
          method: "get",
          url:
                  "index.php?option=com_emundus_onboard&controller=form&task=getFormsByProfileId",
          params: {
            profile_id: profile_id
          },
          paramsSerializer: params => {
            return qs.stringify(params);
          }
        }).then(response => {
          this.formList = response.data.data;
          setTimeout(() => {
            this.getDataObject();
            this.getProfileLabel(profile_id);
          },100);
        }).catch(e => {
          console.log(e);
        });
      },

      /**
       * Récupère le nom du formulaire
       */
      getProfileLabel(profile_id) {
        axios({
          method: "get",
          url:
                  "index.php?option=com_emundus_onboard&controller=form&task=getProfileLabelByProfileId",
          params: {
            profile_id: profile_id
          },
          paramsSerializer: params => {
            return qs.stringify(params);
          }
        })
                .then(response => {
                  this.profileLabel = response.data.data.label;
                })
                .catch(e => {
                  console.log(e);
                });
      },

      sendForm() {
        if(this.cid != ""){
          window.location.replace('index.php?option=com_emundus_onboard&view=form&layout=addnextcampaign&cid=' + this.cid + '&index=1');
        } else {
          axios({
            method: "get",
            url:
                    "index.php?option=com_emundus_onboard&controller=form&task=getassociatedcampaign",
            params: {
              pid: this.prid
            },
            paramsSerializer: params => {
              return qs.stringify(params);
            }
          }).then(response => {
            if(response.data.data.length > 0){
              history.go(-1);
            } else {
              this.$modal.show('modalAffectCampaign');
            }
          }).catch(e => {
            console.log(e);
          });
          //history.go(-1);
        }
      },

      // Triggers
      changeGroup(index,rgt){
        this.indexHighlight = index;
        this.rgt = rgt;
        this.elementDisabled = _.isEmpty(this.formObjectArray[this.indexHighlight].object.Groups);
      },
      SomethingChange: function(e) {
        this.dragging = true;
        let rgts = [];
        this.formList.forEach((menu, index) => {
          rgts.push(menu.rgt);
        });
        this.formObjectArray.forEach((item, index) => {
          item.rgt = rgts[index];
        });
        this.reorderItems();
      },
      showElements() {
        if(this.elementDisabled){
          this.addingElement = false;
        } else {
          this.addingElement = !this.addingElement;
        }
      },
      //

      // Draggable pages
      reorderItems(){
        this.formObjectArray.forEach(item => {
          axios({
            method: "post",
            url:
                    "index.php?option=com_emundus_onboard&controller=formbuilder&task=reordermenu",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            data: qs.stringify({
              rgt: item.rgt,
              link: item.link
            })
          }).catch(e => {
            console.log(e);
          });
        });
      },
      enableGrab(index){
        this.indexGrab = index;
        this.grab = true;
      },
      disableGrab(){
        this.indexGrab = 0;
        this.grab = false;
      },
      //
    },
    created() {
      this.getForms(this.prid);
    },

    computed: {
      dragOptions() {
        return {
          group: {
            name: "items",
            pull: "clone",
            put: false
          },
          sort: false,
          disabled: false,
          ghostClass: "ghost"
        };
      }
    }
  };
</script>

<style scoped lang="scss">
  .fa-li {
    left: -0.45em;
  }

  .full-width {
    width: 100vw;
    position: relative;
    margin-left: -50vw !important;
    left: 50%;
    margin-top: -4.2%;
  }
  .container {
    margin-bottom: 5%;
  }
  h1 {
    margin: 20px;
    line-height: 20px;
    font-family: "Open Sans", sans-serif;
    box-sizing: border-box;
  }
  .sidebar {
    padding-top: 20px;
    background-color: #f0f0f0;
    height: 100%;
    width: 16.9%;
  }
  body {
    background-color: #fafafa;
  }
  .Topbar {
    text-align: center;
    font-family: "Open Sans", sans-serif;
    padding: 25px 0;
    background-color: #f0f0f0;
    height: 150px;
  }
  .separator {
    border-right: 1px solid hsla(0, 0%, 81%, 0.5);
  }

  .btnreturn {
    position: relative;
    left: 37%;
    top: 5%;
    background-color: #1b1f3c;
    border-radius: 28px;
    border: 1px solid #1b1f3c;
    display: inline-block;
    cursor: pointer;
    color: #ffffff;
    font-family: Arial;
    font-size: 17px;
    padding: 12px 27px;
    text-decoration: none;
  }
  .btnreturn:hover {
    background-color: #ef6d3b;
    border: 1px solid #ef6d3b;
  }

  .form-builder{
    margin-top: 6em;
    padding: 1em;
    min-height: 50em;
  }

  .form-title{
    text-align: center;
    padding: 1em;
  }

  @media (max-width: 700px) {
    .form-title{
      max-width: 200px;
    }
  }
  .select-form{
    display: flex;
  }
  .select-form select{
    width: 75%;
    margin-left: 1em;
  }

  .add-menu{
    display: flex;
    justify-content: center;
    align-items: center;
    border: unset;
    cursor: pointer;
    align-self: baseline;
  }

  .add-menu:hover > .btnPM {
    background-color: #1b1f3c;
    color: white;
  }

  .dropdown-toggle{
    height: auto;
    background: white;
  }


  .draggables-list{
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    align-self: baseline;
  }
  .divider-menu{
    width: 100%;
    margin: 0em;
  }
  .heading-block{
    text-align: center;
    margin-bottom: 1em;
    margin-top: 2em;
    width: 75%;
  }
  .edit-icon{
    align-items: center;
    display: flex;
    justify-content: center;
  }
  .container-fluid{
    margin-bottom: 10em;
  }
  .icon-handle{
    color: #cecece;
    position: absolute;
    cursor: grab;
    top: 22px;
  }
  .heading-actions{
    background: #1b1f3c;
    height: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    color: #fff;
  }
  .action-link{
    padding: 1em 10px 10px 5px;
    cursor: pointer;
  }
  .action-link:hover > .action-label{
    color: #de6339;
  }
  .action-links{
    background: #fafafa;
  }
  .form-viewer-builder{
    background: #fafafa;
  }
  .action-label{
    color: black;
    cursor: pointer;
  }
  .disable-element{
    filter: grayscale(1);
    color: gray;
  }
  .fa-pencil-alt{
    color: #de6339;
    cursor: pointer;
  }
</style>
