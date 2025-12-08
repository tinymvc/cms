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

  Alpine.data("fileUpload", (name, value) => ({
    preview: value || null,
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

  Alpine.data("tableData", (rows, baseUrl) => ({
    selectedRows: [],

    toggleRow(id) {
      const index = this.selectedRows.indexOf(id);
      if (index > -1) {
        this.selectedRows.splice(index, 1);
      } else {
        this.selectedRows.push(id);
      }
    },

    toggleAll(event) {
      if (event.target.checked) {
        // Select all visible rows
        this.selectedRows = rows;
      } else {
        this.selectedRows = [];
      }
    },

    performBulkAction(action) {
      if (this.selectedRows.length === 0) {
        alert("Please select at least one item");
        return;
      }

      if (
        confirm(
          `Are you sure you want to ${action} ${this.selectedRows.length} item(s)?`
        )
      ) {
        // Submit bulk action
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `${baseUrl}/bulk/${action}`;

        const csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = document.head.querySelector(
          'meta[name="csrf-token"]'
        ).content;
        form.appendChild(csrfInput);

        const idsInput = document.createElement("input");
        idsInput.type = "hidden";
        idsInput.name = "ids";
        idsInput.value = JSON.stringify(this.selectedRows);
        form.appendChild(idsInput);

        document.body.appendChild(form);
        form.submit();
      }
    },
  }));
});

Alpine.start();
