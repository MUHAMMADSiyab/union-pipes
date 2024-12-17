<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col
                    xl="5"
                    lg="5"
                    md="5"
                    sm="12"
                    cols="12"
                    class="playlist-sidebar"
                >
                    <v-card :loading="loading">
                        <v-card-title>Video Tutorials</v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item
                                    v-for="video in sortedVideos"
                                    :key="video.id"
                                    @click="selectVideo(video)"
                                    :class="{
                                        'active-video':
                                            currentVideo &&
                                            currentVideo.id === video.id,
                                    }"
                                >
                                    <v-list-item-avatar rounded>
                                        <v-img
                                            :src="video.thumbnail"
                                            class="rounded-thumbnail"
                                        ></v-img>
                                    </v-list-item-avatar>
                                    <v-list-item-content>
                                        <v-list-item-title>{{
                                            video.title
                                        }}</v-list-item-title>
                                        <v-list-item-title class="subtext">
                                            <v-icon
                                                style="
                                                    margin-bottom: 2px;
                                                    font-size: 0.82rem;
                                                "
                                                >mdi-clock-time-eight-outline</v-icon
                                            >
                                            {{
                                                convertYouTubeDuration(
                                                    video.duration
                                                )
                                            }}</v-list-item-title
                                        >
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col
                    xl="7"
                    lg="7"
                    md="7"
                    sm="12"
                    cols="12"
                    class="video-player"
                >
                    <div v-if="currentVideo">
                        <v-card :loading="loading">
                            <v-card-title>{{
                                currentVideo.title
                            }}</v-card-title>
                            <v-card-text>
                                <iframe
                                    width="100%"
                                    height="500"
                                    :src="`https://www.youtube.com/embed/${currentVideo.id}`"
                                    frameborder="0"
                                    allowfullscreen
                                ></iframe>
                                <p class="subtext">
                                    {{ currentVideo.description }}
                                </p>
                            </v-card-text>
                        </v-card>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import Navbar from "../navs/Navbar";

export default {
    name: "PlaylistView",
    components: {
        Navbar,
    },
    data() {
        return {
            loading: true,
        };
    },

    computed: {
        ...mapGetters({
            allVideos: "playlist/allVideos",
            currentVideo: "playlist/currentVideo",
        }),
        videos() {
            return this.allVideos;
        },
        sortedVideos() {
            return this.videos.slice().sort((a, b) => {
                const numA = parseInt(a.title.match(/^\d+/)?.[0] || 0);
                const numB = parseInt(b.title.match(/^\d+/)?.[0] || 0);
                return numA - numB;
            });
        },
    },
    methods: {
        ...mapActions({
            fetchPlaylistVideos: "playlist/fetchPlaylistVideos",
            setCurrentVideo: "playlist/setCurrentVideo",
        }),
        selectVideo(video) {
            this.setCurrentVideo(video);
        },
        convertYouTubeDuration(duration) {
            const match = duration.match(/PT(?:(\d+)M)?(?:(\d+)S)?/);

            const minutes = parseInt(match[1] || 0, 10);
            const seconds = parseInt(match[2] || 0, 10);

            const formattedMinutes = minutes.toString().padStart(2, "0");
            const formattedSeconds = seconds.toString().padStart(2, "0");

            return `${formattedMinutes}:${formattedSeconds}`;
        },
    },
    async created() {
        await this.fetchPlaylistVideos();
        this.loading = false;
    },
};
</script>

<style scoped>
.playlist-sidebar {
    max-height: 80vh;
    overflow-y: auto;
}
.active-video {
    background-color: #f0f0f0;
}
.subtext {
    font-size: 0.8rem !important;
    color: rgb(172, 172, 172);
    font-weight: 500;
}
.v-avatar.v-list-item__avatar.rounded {
    width: 70px !important;
    height: 70px !important;
    object-fit: contain;
}
</style>
