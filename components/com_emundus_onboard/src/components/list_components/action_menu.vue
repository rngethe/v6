<template>
  <div class="section-sub-menu">
    <div class="container-2 w-container" style="max-width: unset">
      <div class="w-row">
        <div class="actions-add-block">
          <div data-hover="1"
               data-delay="0"
               class="dropdown w-dropdown"
               @mouseover="actionHover = true"
               @mouseleave="actionHover = false"
               style="margin-right: 3em;">
            <div v-show="isEmpty" class="dropdown-toggle-2 w-dropdown-toggle">
              <div class="icon w-icon-dropdown-toggle"></div>
              <div>{{ Action }}</div>
            </div>
            <nav aria-label="action" v-if="actionHover" class="dropdown-list w-dropdown-list">
              <a v-on:click="publishSelected(checkItem)" class="action-submenu w-dropdown-link" v-if="data.type !== 'form'">
                {{ ActionPublish }}
              </a>
              <a v-on:click="unpublishSelected(checkItem)" class="action-submenu w-dropdown-link" v-if="data.type !== 'form'">
                {{ ActionUnpublish }}
              </a>
              <a v-on:click="publishSelected(checkItem)" class="action-submenu w-dropdown-link" v-if="data.type === 'form'">
                {{ Restore }}
              </a>
              <a v-if="data.type === 'campaign' || data.type === 'form'"
                 v-on:click="duplicateSelected(checkItem)"
                 class="action-submenu w-dropdown-link">
                {{ ActionDuplicate }}
              </a>
              <a v-on:click="deleteSelected(checkItem)" class="action-submenu w-dropdown-link" v-if="data.type !== 'form'">
                {{ ActionDelete }}
              </a>
              <a v-on:click="unpublishSelected(checkItem)" class="action-submenu w-dropdown-link" v-if="data.type === 'form'">
                {{ Archive }}
              </a>
            </nav>
          </div>

          <div>
            <a :href="data.add_url" class="bouton-ajouter w-inline-block">
              <div v-if="data.type === 'program'" class="add-button-div">
                {{ AddProgram }}
                <div class="addCampProgEmail"></div>
              </div>
              <div v-if="data.type === 'campaign'" class="add-button-div">
                {{ AddCampaign }}
                <div class="addCampProgEmail"></div>
              </div>
              <div v-if="data.type === 'email'" class="add-button-div">
                {{ AddEmail }}
                <div class="addCampProgEmail"></div>
              </div>
              <div v-if="data.type === 'form'" class="add-button-div">
                {{ AddForm }}
                <div class="addCampProgEmail"></div>
              </div>
            </a>
          </div>
        </div>

        <div class="search">
          <input class="searchTerm"
                 :placeholder="Rechercher"
                 v-model="recherche"
                 @keyup="cherche(recherche) | debounce"
                 @keyup.enter="chercheGo(recherche)"/>
          <a @click="chercheGo(recherche)" class="searchButton"><em class="fa fa-search"></em></a>
        </div>

        <div class="filters-action">
          <div>
            <div data-hover="1"
                 data-delay="0"
                 class="dropdown w-dropdown"
                 @mouseover="sortHover = true"
                 @mouseleave="sortHover = false">
              <div class="dropdown-toggle-2 w-dropdown-toggle">
                <div class="icon w-icon-dropdown-toggle"></div>
                <div>{{ Sort }}</div>
              </div>
              <nav aria-label="sort" v-if="sortHover" class="dropdown-list w-dropdown-list">
                <a @click="sort('DESC');updateSort('DESC');"
                   class="action-submenu w-dropdown-link"
                   :class="tri == 'DESC' ? 'selected' : ''">
                  {{ SortCreasing }}
                </a>
                <a @click="sort('ASC');updateSort('ASC');"
                   :class="tri == 'ASC' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  {{ SortDecreasing }}
                </a>
              </nav>
            </div>
          </div>

          <div>
            <div data-hover="1"
                 data-delay="0"
                 class="dropdown w-dropdown"
                 @mouseover="filterHover = true"
                 @mouseleave="filterHover = false">
              <div class="dropdown-toggle-2 w-dropdown-toggle">
                <div class="icon w-icon-dropdown-toggle"></div>
                <div>{{ Filter }}</div>
              </div>
              <nav aria-label="filter" v-if="filterHover" class="dropdown-list w-dropdown-list">
                <a @click="filter('all');updateFilter('all');"
                   :class="filtre == 'all' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  {{ FilterAll }}
                </a>
                <a v-if="data.type == 'campaign'"
                   @click="filter('notTerminated');updateFilter('notTerminated');"
                   :class="filtre == 'notTerminated' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  {{ FilterOpen }}
                </a>
                <a v-if="data.type == 'campaign'"
                   @click="filter('Terminated');updateFilter('Terminated');"
                   :class="filtre == 'Terminated' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  {{ FilterClose }}
                </a>
                <a @click="filter('Publish');updateFilter('Publish');"
                   :class="filtre == 'Publish' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link"
                   v-if="data.type !== 'form'">
                  {{ FilterPublish }}
                </a>
                <a @click="filter('Unpublish');updateFilter('Unpublish');"
                   :class="filtre == 'Unpublish' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link"
                   v-if="data.type !== 'form'">
                  {{ FilterUnpublish }}
                </a>
                <a @click="filter('Unpublish');updateFilter('Unpublish');"
                   :class="filtre == 'Unpublish' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link"
                   v-if="data.type === 'form'">
                  {{ Archived }}
                </a>
              </nav>
            </div>
          </div>

          <div>
            <div data-hover="1"
                 data-delay="0"
                 class="dropdown w-dropdown"
                 @mouseover="resultsHover = true"
                 @mouseleave="resultsHover = false">
              <div class="dropdown-toggle-2 w-dropdown-toggle">
                <div class="icon w-icon-dropdown-toggle"></div>
                <div>{{ NbResults }}</div>
              </div>
              <nav aria-label="Nb Results" v-if="resultsHover" class="dropdown-list w-dropdown-list">
                <a @click="nbresults(999999);updateDisplay('max');"
                   :class="afficher == 'max' ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  {{ AllResults }}
                </a>
                <a @click="nbresults(10);updateDisplay(10);"
                   :class="afficher == 10 ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  10
                </a>
                <a @click="nbresults(25);updateDisplay(25);"
                   :class="afficher == 25 ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  25
                </a>
                <a @click="nbresults(50);updateDisplay(50);"
                   :class="afficher == 50 ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  50
                </a>
                <a @click="nbresults(100);updateDisplay(100);"
                   :class="afficher == 100 ? 'selected' : ''"
                   class="action-submenu w-dropdown-link">
                  100
                </a>
              </nav>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="loading-form" style="top: 10vh" v-if="loading">
      <Ring-Loader :color="'#de6339'" />
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import Swal from "sweetalert2";
  import "sweetalert2/src/sweetalert2.scss";
  import { list } from "../../store";
  import "@fortawesome/fontawesome-free/css/all.css";
  import "@fortawesome/fontawesome-free/js/all.js";

  const qs = require("qs");

  export default {
    name: "action_menu",

    props: {
      data: Object,
      updateTotal: Function,
      filter: Function,
      sort: Function,
      cherche: Function,
      chercheGo: Function,
      validateFilters: Function,
      nbresults: Function,
      isEmpty: Boolean
    },

    computed: {
      checkItem() {
        return list.getters.selectedItems;
      }
    },

    data() {
      return {
        recherche: "",
        filterHover: false,
        actionHover: false,
        sortHover: false,
        resultsHover: false,
        loading: false,
        NbResults: Joomla.JText._("COM_EMUNDUS_ONBOARD_RESULTS"),
        AllResults: Joomla.JText._("COM_EMUNDUS_ONBOARD_ALL_RESULTS"),
        Action: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTION"),
        ActionPublish: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTION_PUBLISH"),
        ActionUnpublish: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTION_UNPUBLISH"),
        ActionDuplicate: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTION_DUPLICATE"),
        ActionDelete: Joomla.JText._("COM_EMUNDUS_ONBOARD_ACTION_DELETE"),
        Sort: Joomla.JText._("COM_EMUNDUS_ONBOARD_SORT"),
        SortCreasing: Joomla.JText._("COM_EMUNDUS_ONBOARD_SORT_CREASING"),
        SortDecreasing: Joomla.JText._("COM_EMUNDUS_ONBOARD_SORT_DECREASING"),
        Filter: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER"),
        FilterAll: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_ALL"),
        FilterOpen: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_OPEN"),
        FilterClose: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_CLOSE"),
        FilterPublish: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_PUBLISH"),
        FilterUnpublish: Joomla.JText._("COM_EMUNDUS_ONBOARD_FILTER_UNPUBLISH"),
        AddCampaign: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_CAMPAIGN"),
        AddProgram: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_PROGRAM"),
        AddEmail: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_EMAIL"),
        AddForm: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_FORM"),
        AddFiles: Joomla.JText._("COM_EMUNDUS_ONBOARD_ADD_FILES"),
        Rechercher: Joomla.JText._("COM_EMUNDUS_ONBOARD_SEARCH"),
        Archive: Joomla.JText._("COM_EMUNDUS_ONBOARD_ARCHIVE"),
        Archived: Joomla.JText._("COM_EMUNDUS_ONBOARD_ARCHIVED"),
        Restore: Joomla.JText._("COM_EMUNDUS_ONBOARD_RESTORE"),
        filtre: "all",
        tri: "DESC",
        afficher: 25
      };
    },

    methods: {
      updateFilter(filter) {
        this.filtre = filter;
      },

      updateSort(sort) {
        this.tri = sort;
      },

      updateDisplay(lim) {
        this.afficher = lim;
      },

      deleteSelected(id) {
        switch (this.data.type) {
          case "program":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGDELETE"),
              text: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANT_REVERT"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=program&task=deleteprogram",
                  data: qs.stringify({ id })
                })
                        .then(response => {
                          this.loading = false;
                          list.commit("deleteSelected", id);
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGDELETED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                        })
                        .then(() => {
                          axios
                                  .get(
                                          "index.php?option=com_emundus_onboard&controller=program&task=getprogramcount"
                                  )
                                  .then(response => {
                                    this.total = response.data.data;
                                    this.updateTotal(this.total);
                                  });
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });
            break;

          case "campaign":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPDELETE"),
              text: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANT_REVERT"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=campaign&task=deletecampaign",
                  data: qs.stringify({ id })
                })
                        .then(response => {
                          this.loading = false;
                          list.dispatch("deleteSelected", id).then(() => {
                            list.commit("resetSelectedItemsList");
                          });
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPDELETED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                        })
                        .then(() => {
                          axios
                                  .get(
                                          "index.php?option=com_emundus_onboard&controller=campaign&task=getcampaigncount"
                                  )
                                  .then(response => {
                                    this.total = response.data.data;
                                    this.updateTotal(this.total);
                                    this.$parent.validateFilters();
                                  });
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });

            break;

          case "email":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_EMAILDELETE"),
              text: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANT_REVERT"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=email&task=deleteemail",
                  data: qs.stringify({ id })
                })
                        .then(response => {
                          this.loading = false;
                          list.dispatch("deleteSelected", id).then(() => {
                            list.commit("resetSelectedItemsList");
                          });
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_EMAILDELETED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                        })
                        .then(() => {
                          axios
                                  .get("index.php?option=com_emundus_onboard&controller=email&task=getemailcount")
                                  .then(response => {
                                    this.total = response.data.data;
                                    this.updateTotal(this.total);
                                    this.$parent.validateFilters();
                                  });
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });

            break;

          case "form":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMDELETE"),
              text: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANT_REVERT"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=form&task=deleteform",
                  data: qs.stringify({ id })
                })
                        .then(response => {
                          this.loading = false;
                          list.dispatch("deleteSelected", id).then(() => {
                            list.commit("resetSelectedItemsList");
                          });
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMDELETED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                        })
                        .then(() => {
                          axios
                                  .get("index.php?option=com_emundus_onboard&controller=form&task=getformcount")
                                  .then(response => {
                                    this.total = response.data.data;
                                    this.updateTotal(this.total);
                                    this.$parent.validateFilters();
                                  });
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });

            break;
        }
      },

      unpublishSelected(id) {
        switch (this.data.type) {
          case "program":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGUNPUBLISH"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url:
                          "index.php?option=com_emundus_onboard&controller=program&task=unpublishprogram",
                  data: qs.stringify({ id })
                })
                        .then(response => {
                          this.loading = false;
                          list.commit("unpublish", id);
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGUNPUBLISHED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                          this.$parent.validateFilters();
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });
            break;

          case "campaign":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPAIGNUNPUBLISH"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=campaign&task=unpublishcampaign",
                  data: qs.stringify({id})
                })
                        .then(response => {
                          this.loading = false;
                          list.commit("unpublish", id);
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPAIGNUNPUBLISHED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                          this.$parent.validateFilters();
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });
            break;

          case "email":
            axios({
              method: "post",
              url: "index.php?option=com_emundus_onboard&controller=email&task=unpublishemail",
              data: qs.stringify({ id })
            })
                    .then(response => {
                      list.commit("unpublish", id);
                      Swal.fire({
                        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_EMAILUNPUBLISHED"),
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                      });
                      this.$parent.validateFilters();
                    })
                    .catch(error => {
                      console.log(error);
                    });
            break;

          case "form":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMUNPUBLISH"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=form&task=unpublishform",
                  data: qs.stringify({id})
                })
                        .then(response => {
                          this.loading = false;
                          list.commit("unpublish", id);
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMUNPUBLISHED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                          this.$parent.validateFilters();
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });
            break;
        }
      },

      publishSelected(id) {
        switch (this.data.type) {
          case "program":
            axios({
              method: "post",
              url: "index.php?option=com_emundus_onboard&controller=program&task=publishprogram",
              data: qs.stringify({ id })
            })
                    .then(response => {
                      list.commit("publish", id);
                      Swal.fire({
                        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_PROGPUBLISHED"),
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                      });
                      this.$parent.validateFilters();
                    })
                    .catch(error => {
                      console.log(error);
                    });
            break;

          case "campaign":
            axios({
              method: "post",
              url: "index.php?option=com_emundus_onboard&controller=campaign&task=publishcampaign",
              data: qs.stringify({ id })
            })
                    .then(response => {
                      list.commit("publish", id);
                      Swal.fire({
                        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPAIGNPUBLISHED"),
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                      });
                      this.$parent.validateFilters();
                    })
                    .catch(error => {
                      console.log(error);
                    });
            break;

          case "email":
            axios({
              method: "post",
              url: "index.php?option=com_emundus_onboard&controller=email&task=publishemail",
              data: qs.stringify({ id })
            })
                    .then(response => {
                      list.commit("publish", id);
                      Swal.fire({
                        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_EMAILPUBLISHED"),
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                      });
                      this.$parent.validateFilters();
                    })
                    .catch(error => {
                      console.log(error);
                    });
            break;

          case "form":
            axios({
              method: "post",
              url: "index.php?option=com_emundus_onboard&controller=form&task=publishform",
              data: qs.stringify({ id })
            })
                    .then(response => {
                      list.commit("publish", id);
                      Swal.fire({
                        title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMPUBLISHED"),
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                      });
                      this.$parent.validateFilters();
                    })
                    .catch(error => {
                      console.log(error);
                    });
            break;
        }
      },

      duplicateSelected(id) {
        switch (this.data.type) {
          case "campaign":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPAIGNDUPLICATE"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=campaign&task=duplicatecampaign",
                  data: qs.stringify({id})
                })
                        .then(response => {
                          this.loading = false;
                          list.commit("listUpdate", response.data.data);
                          Swal.fire({
                            title: Joomla.JText._("COM_EMUNDUS_ONBOARD_CAMPAIGNDUPLICATED"),
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                          });
                        })
                        .then(() => {
                          axios
                                  .get(
                                          "index.php?option=com_emundus_onboard&controller=campaign&task=getcampaigncount"
                                  )
                                  .then(response => {
                                    this.total = response.data.data;
                                    this.updateTotal(this.total);
                                    this.$parent.validateFilters();
                                  });
                        })
                        .catch(error => {
                          console.log(error);
                        });
              }
            });
            break;

          case "form":
            Swal.fire({
              title: Joomla.JText._("COM_EMUNDUS_ONBOARD_FORMDUPLICATE"),
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#de6339',
              confirmButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_OK"),
              cancelButtonText: Joomla.JText._("COM_EMUNDUS_ONBOARD_CANCEL"),
              reverseButtons: true
            }).then(result => {
              if (result.value) {
                this.loading = true;
                axios({
                  method: "post",
                  url: "index.php?option=com_emundus_onboard&controller=form&task=duplicateform",
                  data: qs.stringify({id})
                }).then(response => {
                  window.location.reload();
                })
              }
            });
            break;
        }
      }
    }
  };
</script>

<style scoped>
  .search {
    width: 245px;
    position: relative;
    display: flex;
  }

  .searchTerm {
    width: 100%;
    border: 1px solid #de6339;
    border-right: none;
    padding: 5px;
    padding-left: 10px;
    height: 43px;
    border-radius: 5px;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    outline: none;
    color: #de6339;
  }

  .searchTerm::placeholder {
    color: #de6339;
  }

  .searchButton {
    width: 40px;
    height: 43px;
    border: 1px solid #de6339;
    background: #de6339;
    text-align: center;
    color: white;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
    font-size: 20px;
  }

  .searchButton svg {
    margin-top: 30%;
  }

  .validate a {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    color: white;
    background: #1b1f3c;
  }

  .selected {
    color: #de6339;
  }

  div nav a:hover {
    cursor: pointer;
  }

  .w-row{
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    margin-bottom: 0;
  }

  @media (max-width: 991px) {
    .search {
      margin-top: 10px;
      margin-left: 10px;
    }
  }
</style>
