import store from "./store";

export const RedirectBasedOnAuthStatus = (to, from, next) => {
    if (to.name !== "login") {
        if (!store.getters["auth/isAuthenticated"]) {
            return next({
                name: "login"
            });
        }

        if (to.meta.gate) {
            if (!store.getters["auth/user_abilities"].includes(to.meta.gate)) {
                return next({ name: "unauthorized" });
            }
        }
    } else if (to.name === "login") {
        if (store.getters["auth/isAuthenticated"]) {
            return next({
                name: "dashboard"
            });
        }
    }

    next();
};
