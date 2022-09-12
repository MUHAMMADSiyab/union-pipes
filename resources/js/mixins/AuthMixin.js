import store from "../store";

export default {
    methods: {
        can(ability) {
            return store.getters["auth/user_abilities"].includes(ability);
        }
    }
};
