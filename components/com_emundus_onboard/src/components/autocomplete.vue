<template>
  <div class="autocomplete">
    <input
      type="text"
      :id="id"
      v-model="search"
      @input="onChange"
      @keydown.down="onArrowDown"
      @keydown.up="onArrowUp"
      @keydown.enter="onEnter"
      :placeholder="year !== '' ? year : name"
      :class="year !== '' ? '' : 'placeholder'"
    />
    <ul v-show="isOpen" class="autocomplete-results">
      <li
        v-for="(result, i) in results"
        :key="i"
        @click="setResult(result)"
        class="autocomplete-result"
        :class="{ 'is-active': i === arrowCounter }"
      >
        {{ result }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "autocomplete",

  props: {
    items: {
      type: Array,
      required: false,
      default: () => []
    },
    name: String,
    year: String,
    id: String,
  },

  data() {
    return {
      search: "",
      results: [],
      isOpen: false,
      sLoading: false,
      arrowCounter: -1
    };
  },

  created() {
    const sleep = milliseconds => {
      return new Promise(resolve => setTimeout(resolve, milliseconds));
    };
    sleep(500).then(() => {
      this.search = this.year;
    });
  },

  methods: {
    onSearching(event) {
      this.$emit("searched", this.search);
    },

    onChange() {
      this.isOpen = true;
      this.filterResults();
      this.onSearching();
    },
    filterResults() {
      this.results = this.items.filter(
        item => item.toLowerCase().indexOf(this.search.toLowerCase()) > -1
      );
    },
    setResult(result) {
      this.search = result;
      this.isOpen = false;
      this.onSearching();
    },
    onArrowDown() {
      if (this.arrowCounter < this.results.length) {
        this.arrowCounter = this.arrowCounter + 1;
      }
    },
    onArrowUp() {
      if (this.arrowCounter > 0) {
        this.arrowCounter = this.arrowCounter - 1;
      }
    },
    onEnter() {
      this.search = this.results[this.arrowCounter];
      this.isOpen = false;
      this.arrowCounter = -1;
      this.onSearching();
    },
    handleClickOutside(evt) {
      if (!this.$el.contains(evt.target)) {
        this.isOpen = false;
        this.arrowCounter = -1;
      }
    }
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
  },
  destroyed() {
    document.removeEventListener("click", this.handleClickOutside);
  }
};
</script>

<style scoped>
.autocomplete {
  position: relative;
  min-height: 55px;
  width: 100%;
  display: block;
  width: 100%;
  height: 38px;
  margin-bottom: 10px;
  font-size: 14px;
  line-height: 1.428571429;
  color: #333333;
  background-color: #ffffff;
}

input {
  display: block;
  width: 100%;
  height: 55px;
  background: none;
  padding: 8px 12px;
}

input::placeholder {
  color: black;
  font-weight: 300;
}

input.placeholder::placeholder {
  color: #9b9b9b;
  font-weight: 300;
}

.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  overflow: auto;
  background-color: white;
  position: relative;
  z-index: 10;
}

.autocomplete-result {
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
}

.autocomplete-result.is-active,
.autocomplete-result:hover {
  background-color: #1B1F3C;
  color: white;
}
</style>
