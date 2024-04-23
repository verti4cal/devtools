import { startStimulusApp } from "@symfony/stimulus-bundle";
import imagedropzone_controller from "./controllers/imagedropzone_controller.js";

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register("imagedropzone", imagedropzone_controller);
