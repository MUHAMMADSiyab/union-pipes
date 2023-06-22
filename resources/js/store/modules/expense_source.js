import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_EXPENSE_SOURCES,
    UPDATE_EXPENSE_SOURCE,
    DELETE_EXPENSE_SOURCE,
    DELETE_EXPENSE_SOURCES,
    GET_EXPENSE_SOURCE,
    NEW_EXPENSE_SOURCE,
} from "../../mutation_constants";

const state = {
    expense_sources: [],
    expense_source: null,
};

const getters = {
    expense_sources: (state) => state.expense_sources,
    expense_source: (state) => state.expense_source,
};

const actions = {
    // Add expense source
    async addExpenseSource({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/expense_sources", data);

            commit(NEW_EXPENSE_SOURCE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Expense Source added successfully",
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

    // Get expense sources
    async getExpenseSources({ commit }) {
        try {
            const res = await axios.get("/api/expense_sources");

            commit(SET_LOADING, false, { root: true });
            commit(GET_EXPENSE_SOURCES, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Get a single expense source
    async getExpenseSource({ commit }, expense_sourceId) {
        try {
            const res = await axios.get(
                `/api/expense_sources/${expense_sourceId}`
            );

            commit(SET_LOADING, false, { root: true });
            commit(GET_EXPENSE_SOURCE, res.data);
        } catch (error) {
            commit(SET_LOADING, false, { root: true });
            console.log(error);
        }
    },

    // Update expense source
    async updateExpenseSource({ dispatch, commit }, data) {
        try {
            const res = await axios.put(
                `/api/expense_sources/${data.id}`,
                data
            );

            commit(UPDATE_EXPENSE_SOURCE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });

            return dispatch(
                "alert/setAlert",
                {
                    type: "success",
                    message: "Expense Source updated successfully",
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

    // Delete expense source
    async deleteExpenseSource({ dispatch, commit }, expense_sourceId) {
        try {
            const res = await axios.delete(
                `/api/expense_sources/${expense_sourceId}`
            );

            commit(DELETE_EXPENSE_SOURCE, expense_sourceId);

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

    // Delete multiple expense sources
    async deleteMultipleExpenseSources({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(
                `/api/expense_sources/delete_multiple`,
                {
                    headers: {},
                    data: { ids },
                }
            );

            commit(DELETE_EXPENSE_SOURCES, ids);

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
    GET_EXPENSE_SOURCES: (state, payload) => (state.expense_sources = payload),

    GET_EXPENSE_SOURCE: (state, payload) => (state.expense_source = payload),

    NEW_EXPENSE_SOURCE: (state, payload) => {
        state.expense_sources.unshift(payload);
    },

    UPDATE_EXPENSE_SOURCE: (state, payload) => {
        const index = state.expense_sources.findIndex(
            (expense_source) => expense_source.id === payload.id
        );
        state.expense_sources.splice(index, 1, payload);
    },

    DELETE_EXPENSE_SOURCE: (state, payload) => {
        const index = state.expense_sources.findIndex(
            (expense_source) => expense_source.id === payload
        );
        state.expense_sources.splice(index, 1);
    },

    DELETE_EXPENSE_SOURCES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.expense_sources.findIndex(
                (expense_source) => expense_source.id === id
            );
            state.expense_sources.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
