import axios from "axios";
import {
    SET_LOADING,
    SET_VALIDATION_ERRORS,
    CLEAR_VALIDATION_ERRORS,
    GET_EXPENSES,
    UPDATE_EXPENSE,
    DELETE_EXPENSE,
    DELETE_EXPENSES,
    GET_EXPENSE,
    NEW_EXPENSE,
    OLD_EXPENSE,
} from "../../mutation_constants";

const state = {
    expenses: [],
    expense: null,
    recent_expense: null,
    old_expense: null,
    loading: false,
    total: 0,
};

const getters = {
    expenses: (state) => state.expenses,
    expense: (state) => state.expense,
    recent_expense: (state) => state.recent_expense,
    old_expense: (state) => state.old_expense,
    loading: (state) => state.loading,
    total: (state) => state.total,
};

const actions = {
    // Add expense
    async addExpense({ dispatch, commit }, data) {
        try {
            const res = await axios.post("/api/expenses", data);

            commit(NEW_EXPENSE, res.data);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }

            console.log(error);
        }
    },

    // Get expenses
    async getExpenses({ commit }, { page, itemsPerPage, sortBy, sortDesc }) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/expenses?page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_EXPENSES, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Search expenses
    async searchExpenses(
        { commit },
        { page, itemsPerPage, sortBy, sortDesc, search }
    ) {
        try {
            commit(SET_LOADING, true);

            const orderBy = sortBy && sortBy.length ? sortBy[0] : "id";
            const orderByDesc =
                sortDesc && sortDesc.length ? sortDesc[0] : true;

            const res = await axios.get(
                `/api/search_expenses?search=${search}&page=${page}&itemsPerPage=${itemsPerPage}&orderBy=${orderBy}&orderByDesc=${orderByDesc}`
            );
            commit(SET_LOADING, false);
            commit(GET_EXPENSES, res.data);
        } catch (error) {
            commit(SET_LOADING, false);
            console.log(error);
        }
    },

    // Update expense
    async updateExpense({ commit }, data) {
        try {
            const res = await axios.put(`/api/expenses/${data.id}`, data);

            commit(UPDATE_EXPENSE, res.data.updated_expense);
            commit(OLD_EXPENSE, res.data.old_expense);
            commit(CLEAR_VALIDATION_ERRORS, _, { root: true });
        } catch (error) {
            if (error.response.status === 422) {
                commit(SET_VALIDATION_ERRORS, error.response.data, {
                    root: true,
                });
            }
            console.log(error);
        }
    },

    // Delete expense
    async deleteExpense({ dispatch, commit }, expenseId) {
        try {
            const res = await axios.delete(`/api/expenses/${expenseId}`);

            commit(DELETE_EXPENSE, expenseId);

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

    // Delete multiple expenses
    async deleteMultipleExpenses({ dispatch, commit }, ids) {
        try {
            const res = await axios.delete(`/api/expenses/delete_multiple`, {
                headers: {},
                data: { ids },
            });

            commit(DELETE_EXPENSES, ids);

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
    SET_LOADING: (state, payload) => (state.loading = payload),

    GET_EXPENSES: (state, payload) => {
        state.expenses = payload.data;
        state.total = payload.total;
    },

    GET_EXPENSE: (state, payload) => (state.expense = payload),

    NEW_EXPENSE: (state, payload) => {
        state.recent_expense = payload;
    },

    OLD_EXPENSE: (state, payload) => {
        state.old_expense = payload;
    },

    UPDATE_EXPENSE: (state, payload) => {
        const index = state.expenses.findIndex(
            (expense) => expense.id === payload.id
        );
        state.expenses.splice(index, 1, payload);
    },

    DELETE_EXPENSE: (state, payload) => {
        const index = state.expenses.findIndex(
            (expense) => expense.id === payload
        );
        state.expenses.splice(index, 1);
    },

    DELETE_EXPENSES: (state, payload) => {
        payload.forEach((id) => {
            const index = state.expenses.findIndex(
                (expense) => expense.id === id
            );
            state.expenses.splice(index, 1);
        });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
