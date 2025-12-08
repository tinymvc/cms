/**
 * aap.js
 *
 * This file is the entry point of the Vite application. It contains the
 * necessary code to initialize the application and mount the root
 * component to the DOM.
 *
 * When the application is built, Vite uses this file as the input and
 * generates a bundle that can be loaded by the browser.
 *
 * @module aap
 */

import "./app.css";

import Alpine from "alpinejs";

import axios from "axios";

window.Alpine = Alpine;

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

document.addEventListener("alpine:init", () => {
  Alpine.data("richEditor", (name, initialContent) => ({
    content: initialContent || "",
    init() {
      this.$refs.editor.innerHTML = this.content;
    },
    format(command, value = null) {
      document.execCommand(command, false, value);
      this.updateValue();
    },
    updateValue() {
      this.content = this.$refs.editor.innerHTML;
    },
  }));

  Alpine.data("fileUpload", (name) => ({
    preview: null,
    handleFileSelect(event) {
      const file = event.target.files[0];
      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.preview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
  }));
});

Alpine.start();
