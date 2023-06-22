import axios from "axios";

export default function subscribeToStore(store) {
    store.subscribe(mutation => {
        switch (mutation.type) {
            case "auth/SET_TOKEN":
                if (mutation.payload) {
                    axios.defaults.headers.common[
                        "Authorization"
                    ] = `Bearer ${mutation.payload}`;

                    // Save token in localStorage
                    localStorage.setItem("token", mutation.payload);
                } else {
                    delete axios.defaults.headers.common["Authorization"];
                    localStorage.removeItem("token");
                }

                break;
        }
    });
}
