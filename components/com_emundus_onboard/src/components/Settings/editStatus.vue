<template>
    <div class="container-evaluation">
        <div v-for="(statu, index) in status" class="status-item">
            <div :style="{background: statu.class}" class="status-field">
                <div style="width: 100%">
                    <input type="text" v-model="statu.value_fr">
                    <div class="translate-block" v-if="statu.translate">
                        <label class="translate-label">
                            {{TranslateEnglish}}
                        </label>
                        <em class="fas fa-sort-down"></em>
                    </div>
                    <input type="text" v-model="statu.value_en" v-if="statu.translate">
                </div>
                <button class="translate-icon" style="height: 10%;margin-top: 10px;" v-bind:class="{'translate-icon-selected': statu.translate}" type="button" @click="statu.translate = !statu.translate; $forceUpdate()"></button>
                <input type="hidden" :class="'label-' + statu.class">
            </div>
            <v-swatches
                    v-model="statu.class"
                    :swatches="swatches"
                    shapes="circles"
                    row-length="8"
                    show-border
                    popover-x="left"
                    popover-y="top"
            ></v-swatches>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import VSwatches from 'vue-swatches'
    import 'vue-swatches/dist/vue-swatches.css'

    const qs = require("qs");

    export default {
        name: "editStatus",

        components: {
            VSwatches
        },

        props: {},

        data() {
            return {
                status: [],
                show: false,
                swatches: [
                    '#DCC6E0', '#947CB0', '#663399', '#6BB9F0', '#19B5FE', '#013243', '#7BEFB2', '#3FC380', '#1E824C', '#FFFD7E',
                    '#FFFD54', '#F7CA18', '#FABE58', '#E87E04', '#D35400', '#EC644B', '#CF000F', '#E5283B', '#E08283', '#D2527F',
                    '#DB0A5B', '#999999'
                ],
                TranslateEnglish: Joomla.JText._("COM_EMUNDUS_ONBOARD_TRANSLATE_ENGLISH"),
            };
        },

        methods: {
            getStatus() {
                axios.get("index.php?option=com_emundus_onboard&controller=settings&task=getstatus")
                    .then(response => {
                        this.status = response.data.data;
                        setTimeout(() => {
                            this.getHexColors();
                        }, 100);
                    });
            },

            getHexColors() {
                this.status.forEach(element => {
                    element.translate = false;
                    let status_class = document.querySelector('.label-' + element.class);
                    let style = getComputedStyle(status_class);
                    let rgbs = style.backgroundColor.split('(')[1].split(')')[0].split(',');
                    element.class = this.rgbToHex(parseInt(rgbs[0]),parseInt(rgbs[1]),parseInt(rgbs[2]));
                });
            },

            rgbToHex(r, g, b) {
                return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
            }
        },

        created() {
            this.getStatus();
        }
    };
</script>
<style>
    .status-item{
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1em;
    }
    .status-field{
        padding: 5px;
        border-radius: 5px;
        width: 100%;
        margin-right: 1em;
        display: flex;
    }
    input {
        background-color: #ececec;
    }
    .translate-block{
        display: flex;
        margin: 10px;
        color: white
    }
</style>
