import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_PARTNER,
    GET_PARTNERS,
    GET_PARTNER,
    UPDATE_PARTNER,
    DELETE_PARTNER,
    DELETE_PARTNERS,
} from "../../mutation_constants";

const state = {
    partners: [],
    partner: null,
};

const getters = {
    partners: (state) => state.partners,
    partner: (state) => state.partner,
};

const actions = {
    // Add partner
    async addPartner(
        { dispatch, commit },
        { name, cnic, phone, address, photo }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("photo", photo);

            const res = await axios.post("/api/partners", fd);

            commit(NEW_PARTNER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Partner added successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get partners
    async getPartners({ commit }) {
        try {
            const res = await axios.get(`/api/partners`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Search partners
    async searchPartners({ commit }, { searchKeyword }) {
        try {
            const res = await axios.post(`/api/partners/search`, {
                searchKeyword,
            });

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get all partners
    async getAllPartners({ commit }, local) {
        try {
            const res = await axios.get(`/api/all_partners`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNERS, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single partner
    async getPartner({ commit }, partnerId) {
        try {
            const res = await axios.get(`/api/partners/${partnerId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_PARTNER, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update partner
    async updatePartner(
        { dispatch, commit },
        { id, name, cnic, phone, address, photo, local }
    ) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("cnic", cnic);
            fd.append("phone", phone);
            fd.append("address", address);
            fd.append("photo", photo);
            fd.append("_method", "PUT");

            const res = await axios.post(`/api/partners/${id}`, fd);

            commit(UPDATE_PARTNER, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Partner updated successfully",
                },
                { root: true }
            );
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete partner
    async deletePartner({ dispatch, commit }, partnerId) {
        try {
            const res = await axios.delete(`/api/partners/${partnerId}`);

            commit(DELETE_PARTNER, partnerId);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },

    // Delete multiple partners
    async deleteMultiplePartners({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/partners/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_PARTNERS, ids);

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: res.data.success,
                },
                { root: true }
            );
        } catch (error) {
            console.log(error);
        }
    },
};

const mutations = {
    GET_PARTNERS: (state, payload) => (state.partners = payload),

    GET_PARTNER: (state, payload) => (state.partner = payload),

    NEW_PARTNER: (state, payload) => {
        state.partners.unshift(payload);
    },

    UPDATE_PARTNER: (state, payload) => {
        const index = state.partners.findIndex(
            (partner) => partner.id === payload.id
        );
        state.partners.splice(index, 1, payload);
    },

    DELETE_PARTNER: (state, payload) => {
        const index = state.partners.findIndex(
            (partner) => partner.id === payload
        );
        state.partners.splice(index, 1);
    },

    DELETE_PARTNERS: (state, payload) => {
        payload.forEach((id) => {
            const index = state.partners.findIndex(
                (partner) => partner.id === id
            );
            state.partners.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
