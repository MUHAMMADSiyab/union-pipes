import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_APP_SETTING
} from "../../mutation_constants";

const state = {
    app_setting: null
};

const getters = {
    app_setting: state => state.app_setting
};

const actions = {
    // Get a app setting
    async getAppSetting({ commit }) {
        try {
            const res = await axios.get(`/api/settings`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_APP_SETTING, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update app setting
    async updateAppSetting(
        { dispatch, commit },
        { id, app_name, phone, fax, email, address, app_logo }
    ) {
        try {
            const fd = new FormData();

            fd.append("app_name", app_name);
            fd.append("phone", phone);
            fd.append("email", email);
            fd.append("fax", fax);
            fd.append("address", address);
            fd.append("app_logo", app_logo);
            fd.append("_method", "PUT");

            const res = await axios.post(`/api/settings/${id}`, fd);

            commit(GET_APP_SETTING, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Setting updated successfully"
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true
                });
            }
            console.log(error);
        }
    }
};

const mutations = {
    GET_APP_SETTING: (state, payload) => (state.app_setting = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
