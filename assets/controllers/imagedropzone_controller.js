import { Controller } from "@hotwired/stimulus";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
  connect() {
    this.element.addEventListener("dropzone:connect", this._onConnect);
    this.element.addEventListener("dropzone:change", this._onChange);
    this.element.addEventListener("dropzone:clear", this._onClear);
  }

  disconnect() {
    // You should always remove listeners when the controller is disconnected to avoid side-effects
    this.element.removeEventListener("dropzone:connect", this._onConnect);
    this.element.removeEventListener("dropzone:change", this._onChange);
    this.element.removeEventListener("dropzone:clear", this._onClear);
  }

  _onConnect(event) {
    // The dropzone was just created
  }

  _onChange(event) {
    // The dropzone just changed
  }

  _onClear(event) {
    // The dropzone has just been cleared
  }
}
