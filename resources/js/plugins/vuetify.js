import Vue from "vue";
import Vuetify from "vuetify";

Vue.use(Vuetify);

export default new Vuetify({
    icons: {
        iconfont: "mdi",
    },

    theme: {
        dark: localStorage.getItem("dark") == "true",
        themes: {
            light: {
                primary: "#1A68D2", // #8500dd
                lighter: "#D2E3FC",
                default: "#ededed",
                app_bg: "#f7f7f7",
                black: "#000000",
            },

            dark: {
                primary: "#1E1E1E",
                app_bg: "#1c1c1c",
                black: "#ffffff",
            },
        },
    },
});
