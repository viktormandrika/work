<template>
    <div>
        <v-subheader class="all-head">
            Ваши резюме
            <router-link class="vacancy__link" to="/personal-area/add-resume">
                <v-btn class="vacancy__link">
                    Добавить резюме
                </v-btn>
            </router-link>
        </v-subheader>
        <template v-if="getAllResume.length === 0">
            <v-subheader>У вас нет резюме</v-subheader>
        </template>

        <template v-else>
            <div>
                <div class="all-resume">

                    <v-list two-line>

                        <v-list-tile
                                v-for="(item, index) in getAllResume"
                                :key="index"
                                style="margin-top: 20px;"
                        >
                            <v-list-tile-avatar>
                                <img :src="item.image_url" alt="">
                            </v-list-tile-avatar>

                            <v-list-tile-content>
                                <v-list-tile-title class="mt-auto mb-auto">
                                    <a :href="domen + '/resume/view/' + item.id" target="_blank">
                                        {{ item.title | capitalize }}
                                    </a>
                                </v-list-tile-title>
                                <v-list-tile-sub-title class="mt-auto mb-auto">Последнее обновление: {{ item.update_time
                                    }}
                                </v-list-tile-sub-title>
                                <v-divider style="width: 100%;"></v-divider>
                                <span v-if="item.status == 2" class="all-resume-hide">скрыто</span>
                            </v-list-tile-content>
                            <router-link :to="`${editLink}/${item.id}`">
                                <v-btn outline small fab
                                       class="edit-btn"
                                       type="button"
                                       title="Редактировать"
                                >
                                    <v-icon>edit</v-icon>

                                </v-btn>
                            </router-link>
                            <v-btn outline small fab
                                   v-bind:disabled="item.can_update == false"
                                   class="edit-btn"
                                   type="button"
                                   title="Поднять в ТОП"
                                   @click="updateResume(index, item.id)"
                            >
                                <v-icon>arrow_upward</v-icon>
                            </v-btn>
                            <v-btn outline small fab
                                   class="edit-btn"
                                   type="button"
                                   title="Удалить"
                                   @click="removeResume(index, item.id)"
                            >
                                <v-icon>delete</v-icon>

                            </v-btn>
                        </v-list-tile>
                    </v-list>

                </div>

                <template v-if="paginationPageCount > 1">
                    <div class="text-xs-center">
                        <v-pagination
                                v-model="paginationCurrentPage"
                                :length="paginationPageCount"
                                @input="changePage"
                        ></v-pagination>
                    </div>
                </template>

            </div>
        </template>
    </div>

</template>

<script>

    export default {
        name: "AllResume",
        data() {
            return {
                editLink: '/personal-area/edit-resume',
                getAllResume: [],
                paginationPageCount: 1,
                paginationCurrentPage: 1,
                domen: ''
            }
        },
        created() {
            document.title = this.$route.meta.title;
            this.$http.get(`${process.env.VUE_APP_API_URL}/request/resume/my-index?expand=can_update&sort=-update_time`)
                .then(response => {
                        this.domen = `${process.env.VUE_APP_API_URL}`;
                        this.getAllResume = response.data;
                        this.getAllResume.forEach((element) => {
                            let timestamp = element.update_time;
                            let date = new Date();
                            date.setTime(timestamp * 1000);
                            let options = {
                                year: 'numeric',
                                month: 'numeric',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                            };
                            element.update_time = date.toLocaleString("ru", options);
                        });
                        this.paginationPageCount = response.headers.map['x-pagination-page-count'][0];
                    }, response => {
                        this.$swal({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 4000,
                            type: 'error',
                            title: response.data.message
                        })
                    }
                );

        },
        methods: {
            updateResume(index, resumeId) {
                this.$http.post(`${process.env.VUE_APP_API_URL}/request/resume/update-time`, {id: resumeId})
                    .then(response => {
                            this.getAllResume.splice(index, 1);
                            let newData = response.data;
                            let date = new Date();
                            date.setTime(newData.update_time * 1000);
                            let options = {
                                year: 'numeric',
                                month: 'numeric',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                            };
                            response.data.update_time = date.toLocaleString("ru", options);
                            ym(53666866,'reachGoal','resume_to_top');
                            this.getAllResume.unshift(newData);
                        }, response => {
                            this.$swal({
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 4000,
                                type: 'error',
                                title: response.data.message
                            })
                        }
                    );
            },
            removeResume(index, resumeId) {
                this.$swal({
                    title: 'Вы точно хотите удалить резюме?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да',
                    cancelButtonText: 'Нет'
                }).then((result) => {
                    if (result.value) {
                        this.getAllResume.splice(index, 1);
                        this.$http.delete(`${process.env.VUE_APP_API_URL}/request/resume/` + resumeId)
                            .then(response => {
                                    return response;
                                }, response => {
                                    this.$swal({
                                        toast: true,
                                        position: 'bottom-end',
                                        showConfirmButton: false,
                                        timer: 4000,
                                        type: 'error',
                                        title: response.data.message
                                    })
                                }
                            );
                    }
                });
            },
            async changePage(paginationCurrentPage) {
                await this.$http.get(`${process.env.VUE_APP_API_URL}/request/resume/my-index?page=` + paginationCurrentPage)
                    .then(response => {
                            this.getAllResume = response.data;
                        }, response => {
                            this.$swal({
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 4000,
                                type: 'error',
                                title: response.data.message
                            })
                        }
                    );
            }
        },
        filters: {
            capitalize(val) {
                if (!val) {
                    return '';
                }

                val = val.toString();

                return val.charAt(0).toUpperCase() + val.slice(1);
            },
        }
    }
</script>

<style scoped>
    .all-resume .theme--light.v-list {
        background-color: transparent;
    }

    a {
        text-decoration: none;
    }

    .all-head {
        margin-top: 10px;
        margin-bottom: 15px;
        padding: 0;
        font-size: 22px;
        color: rgba(0, 0, 0, .74);
    }

    .all-head a {
        margin-left: 15px;
    }

    .all-head a button {
        text-transform: none !important;
    }
    .v-list__tile__content {
        position: relative;
    }
    .all-resume-hide {
        position: absolute;
        right: 40px;
        padding: 3px;
        font-size: 13px;
        color: rgba(0,0,0,0.62);
        line-height: 1;
        border-radius: 6px;
        border: 1px solid rgba(0,0,0,0.12);
    }
</style>