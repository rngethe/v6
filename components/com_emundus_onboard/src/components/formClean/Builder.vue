<template>
  <div id="FormBuilder" class="container-fluid">
    <div class="row">
      <BuilderViewer
        class="col-md-12"
        :object="object"
        :groups="GroupList"
        v-if="object"
        :change="this.change"
        :changedElement="this.changedElement"
        :changedGroup="this.changedGroup"
        @show="show"
        :UpdateUx="UpdateUx"
        @UpdateUxf="UpdateUXF"
        :key="builderViewKey"
        ref="builder_viewer"
      />
    </div>
  </div>
</template>


<script>
import axios from "axios";
import draggable from "vuedraggable";
import BuilderViewer from "./BuilderView";
import ModalElement from "./ModalElement";
import Swal from "sweetalert2";

const qs = require("qs");

export default {
  name: "FormBuilder",
  props: { object: Object, UpdateUx: Boolean, rgt: Number  },
  components: {
    draggable,
    BuilderViewer,
    ModalElement,
  },
  data() {
    return {
      newlabel: [],
      dblckickLabel: [],
      testastos: [],
      ElementList: [],
      GroupList: [],
      object_json: Object,
      GroupsObject: Object,
      change: false,
      changedElement: "",
      changedGroup: "",
      update: false,
      builderViewKey: 0,
      addGroup: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDGROUP"),
      addItem: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_ADDITEM"),
      editGroup: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_EDITGROUP"),
      deleteGroup: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_DELETEGROUP"),
      deleteMenu: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_DELETEMENU"),
    };
  },
  methods: {
    UpdateUXT() {
      this.UpdateUx = true;
    },
    UpdateUXF() {
      this.UpdateUx = false;
    },
    UpdateLabel(element, label) {
      element.label_raw = label;
    },
    UpdateGroupName(group, label) {
      this.object_json.Groups[group].group_showLegend = label;
    },
    UpdateBuilderView() {
      this.$emit("UpdateFormBuilder");
    },
    show(group, type, text, title) {
      this.$emit("show", group, type, text, title);
    },
    Initialised: function() {
      for (var group in this.GroupsObject) {
        let IndexTable = this.object.rgt + "_" + this.GroupsObject[group].group_id;
        this.ElementList[IndexTable] = Object.values(
          this.GroupsObject[group].elements
        );
      }
    },
    deleteAMenu(mid){
      Swal.fire({
        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_DELETEMENU"),
        text: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_DELETEMENUWARNING"),
        type: "warning",
        showCancelButton: true
      }).then(result => {
        if (result.value) {
          axios({
            method: "post",
            url:
                    "index.php?option=com_emundus_onboard&controller=formbuilder&task=deleteMenu",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            data: qs.stringify({
              mid: mid,
            })
          }).then(() => {
            this.$modal.hide('modalSide' + this.ID)
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_BUILDER_MENUDELETED"),
              type: "success",
              showConfirmButton: false,
              timer: 2000
            }).then(() => {
              this.$emit("UpdateFormBuilder");
            });
          }).catch(e => {
            console.log(e);
          });
        }
      });
    },
    async updateOrder(gid,elts){
      await this.$refs.builder_viewer.updateElementsOrder(gid,elts);
    },

    getDataObject: function() {
      this.object_json = this.object.object;
      this.GroupList = Object.values(this.object_json.Groups);
      this.GroupsObject = this.object_json.Groups;
      this.Initialised();
    }
  },
  created() {
    this.getDataObject();
  },
  watch: {
    object: function() {
      this.getDataObject();
    },
    update: function() {
      if (this.update === true) {
        this.getDataObject();
      }
    },
    UpdateUx: function() {
      if (this.UpdateUx === true) {
        this.UpdateUXT();
      }
    },
  }
};
</script>

<style scoped>
  #FormBuilder{
    margin-bottom: unset !important;
  }
</style>
