import axios from "axios";
import { SET_CURRENT_VIDEO, SET_VIDEOS } from "../../mutation_constants";

const state = {
    videos: [],
    currentVideo: null,
};

const getters = {
    allVideos: (state) => state.videos,
    currentVideo: (state) => state.currentVideo,
};

const actions = {
    async fetchPlaylistVideos({ commit }) {
        try {
            const response = await axios.get("/api/youtube/playlist");
            commit(SET_VIDEOS, response.data);

            if (response.data.length > 0) {
                commit(SET_CURRENT_VIDEO, response.data[0]);
            }
        } catch (error) {
            console.error("Error fetching playlist videos:", error);
        }
    },

    setCurrentVideo({ commit }, video) {
        commit(SET_CURRENT_VIDEO, video);
    },
};

const mutations = {
    SET_VIDEOS: (state, videos) => {
        state.videos = videos;
    },
    SET_CURRENT_VIDEO: (state, video) => {
        state.currentVideo = video;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
