import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    NEW_COMPANY,
    GET_COMPANIES,
    GET_COMPANY,
    UPDATE_COMPANY,
    DELETE_COMPANY,
    DELETE_COMPANIES,
    GET_LEDGER_ENTRIES,
} from "../../mutation_constants";

const state = {
    companies: [],
    company: null,
    ledger_entries: [],
};

const getters = {
    companies: (state) => state.companies,
    company: (state) => state.company,
    ledger_entries: (state) => state.ledger_entries,
};

const actions = {
    // Add company
    async addCompany({ dispatch, commit }, { name, description, logo }) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("description", description);
            fd.append("logo", logo);

            const res = await axios.post("/api/companies", fd);

            commit(NEW_COMPANY, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Company added successfully",
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

    // Get companies
    async getCompanies({ commit }) {
        try {
            const res = await axios.get("/api/companies");

            commit(SET_LOADING, false, { root: true });
            commit(GET_COMPANIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Search companies
    async searchCompanies({ commit }, searchKeyword) {
        try {
            const res = await axios.post("/api/companies/search", {
                searchKeyword,
            });

            commit(SET_LOADING, false, { root: true });
            commit(GET_COMPANIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single company
    async getCompany({ commit }, companyId) {
        try {
            const res = await axios.get(`/api/companies/${companyId}`);

            commit(SET_LOADING, false, { root: true });
            commit(GET_COMPANY, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get compnay's ledger entries data
    async getLedgerEntries({ commit }, companyId) {
        try {
            const res = await axios.post(
                `/api/companies/${companyId}/ledger_entries`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_LEDGER_ENTRIES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update company
    async updateCompany({ dispatch, commit }, { id, name, description, logo }) {
        try {
            const fd = new FormData();

            fd.append("name", name);
            fd.append("description", description);
            fd.append("logo", logo);
            fd.append("_method", "PUT");

            const res = await axios.post(`/api/companies/${id}`, fd);

            commit(UPDATE_COMPANY, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Company updated successfully",
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

    // Delete company
    async deleteCompany({ dispatch, commit }, companyId) {
        try {
            const res = await axios.delete(`/api/companies/${companyId}`);

            commit(DELETE_COMPANY, companyId);

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

    // Delete multiple companies
    async deleteMultipleCompanies({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/companies/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_COMPANIES, ids);

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
    GET_COMPANIES: (state, payload) => (state.companies = payload),

    GET_COMPANY: (state, payload) => (state.company = payload),

    NEW_COMPANY: (state, payload) => {
        state.companies.unshift(payload);
    },

    UPDATE_COMPANY: (state, payload) => {
        const index = state.companies.findIndex(
            (company) => company.id === payload.id
        );
        state.companies.splice(index, 1, payload);
    },

    GET_LEDGER_ENTRIES: (state, payload) => (state.ledger_entries = payload),

    DELETE_COMPANY: (state, payload) => {
        const index = state.companies.findIndex(
            (company) => company.id === payload
        );
        state.companies.splice(index, 1);
    },

    DELETE_COMPANIES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.companies.findIndex(
                (company) => company.id === id
            );
            state.companies.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
