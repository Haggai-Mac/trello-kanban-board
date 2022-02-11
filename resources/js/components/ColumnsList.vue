<template>
    <div class="columns-wrapper">
        <template v-if="errors.length > 0">
            <div v-for="(error, idx) in errors" :key="idx" class="alert alert-danger">{{ error }}</div>
        </template>
        <template v-if="messages.length > 0">
            <div v-for="(message, idx) in messages" :key="idx" class="alert alert-success">{{ message }}</div>
        </template>
        <div v-for="(column, index) in currentList" :key="column.id" class="column">
            <div class="column-wrapper">
                <div class="column-header">
                    <div @click="editColumn(column, index)" :class="['column-header-target', (editAction ? 'is-hidden' : '')]"></div>
                    <textarea v-model="column.title" ref="title" @blur="updateColumn(column)" @change="changeColumn(column)"></textarea>
                    <a class="delete-column" @click="deleteColumn(column)">X</a>
                </div>
                <div class="column-cards">
                    <draggable v-model="column.cards" class="list-group" group="cards" @change="updateOrder" item-key="id">
                        <template #item="{element}">
                            <div class="column-card" @click="openCardModal(element)">
                                <span>{{ element.title }}</span>
                                <vue-final-modal v-model="element.show_modal" :name="'cardModal-' + element.id" classes="modal-container" content-class="modal-content" @click-outside="closeModal(element)" @closed="closeModal(element)" @cancel="closeModal(element)">
                                    <span class="modal__title">{{ element.title }}</span>
                                    <div class="modal__content">
                                        <form @submit.prevent="updateCard(element)">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" v-model="element.title" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea v-model="element.description"></textarea>
                                            </div>
                                            <input type="submit" value="Update card" :disabled="loading">
                                            <a class="delete-card" @click="deleteCard(element)">Delete Card</a>
                                        </form>
                                    </div>
                                </vue-final-modal>
                            </div>
                        </template>
                    </draggable>
                    <div :class="['column-card', 'add-card', (column.add_new_card ? '' : 'hide')]">
                        <form @submit.prevent="saveCard(column)">
                            <textarea v-model="newCard.title" required ref="newCard" placeholder="Enter a title for this card…"></textarea>
                            <div class="column-card-add-controls">
                                <input type="submit" value="Add card" :disabled="loading">
                                <a class="js-cancel-edit" @click="toggleAddNewCard(column, index)">X</a>
                            </div>
                        </form>
                    </div>
                </div>
                <a class="column-add-card" v-if="!column.add_new_card" @click="toggleAddNewCard(column, index)">Add a card</a>
            </div>
        </div>
        <div :class="['column', 'add-column', (addNewColumn ? '' : 'is-idle')]">
            <form @submit.prevent="saveColumn">
                <input v-if="addNewColumn" class="column-title-input" type="text" ref="newColumn" required v-model="newColumn.title" placeholder="Enter column title…" autocomplete="off" dir="auto" maxlength="512">
                <div v-if="addNewColumn" class="column-add-controls">
                    <input type="submit" value="Add column" :disabled="loading">
                    <a class="js-cancel-edit" @click="toggleAddNewColumn()">X</a>
                </div>
                <a v-if="!addNewColumn" class="open-add-column" @click="toggleAddNewColumn()">
                    <span class="placeholder">
                        Add Column
                    </span>
                </a>
            </form>
        </div>
    </div>
</template>
<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    props: {
        getColumnsPath: {
            type: String,
            required: true
        },
        saveNewColumnPath: {
            type: String,
            required: true
        },
        saveNewCardPath: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            loading: true,
            currentList: [],
            errors: [],
            messages: [],
            addNewColumn: false,
            editAction: false,
            newColumn: {
                title: '',
            },
            newCard: {
                column_id: null,
                title: '',
                description: ''
            },
            oldColumnID: null,
        }
    },
    mounted() {
        this.fetchData(this.getColumnsPath);
    },
    methods: {
        fetchData(url) {
            this.errors = [];
            this.loading = true;
            axios
                .get(url)
                .then(e => {
                    this.currentList = e.data.columns;
                }).then(r => {
                    this.loading = false;
                }).catch(error => {
                    if (error.response != undefined && error.response.data != undefined) {
                        this.currentList = [];

                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }

                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
                });
        },

        toggleAddNewColumn() {
            this.addNewColumn = !this.addNewColumn;
            this.$nextTick(() => {
                this.$refs["newColumn"].focus()
            });
        },

        saveColumn() {
            this.loading = true;
            this.errors = [];
            this.messages = [];

            axios
                .post(this.saveNewColumnPath, this.newColumn)
                .then(response => {
                    this.loading = false;

                    if(response.data != undefined && response.data.message != undefined) {
                        if(typeof response.data.message == "string"){
                            this.messages.push(response.data.message);
                        } else {
                            this.messages = response.data.message;
                        }
                    }

                    this.addNewColumn = false;
                    this.newColumn.title = '';
                    this.fetchData(this.getColumnsPath);
                }).catch(error => {
                    if(error.response != undefined) {
                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }
                        
                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
            });
        },

        deleteColumn(column) {
            this.loading = true;
            this.errors = [];
            this.messages = [];

            axios
                .delete(column.delete_path, column)
                .then(response => {
                    this.loading = false;

                    if(response.data != undefined && response.data.message != undefined) {
                        if(typeof response.data.message == "string"){
                            this.messages.push(response.data.message);
                        } else {
                            this.messages = response.data.message;
                        }
                    }

                    this.fetchData(this.getColumnsPath);
                }).catch(error => {
                    if(error.response != undefined) {
                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }
                        
                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
            });
        },

        editColumn(column, index) {
            this.editAction = true;
            this.errors = [];
            this.messages = [];

            this.$nextTick(() => {
                this.$refs.title[index].focus()
            });
        },

        changeColumn(column) {
            column.changed = true;
        },

        updateColumn(column) {
            this.editAction = false;
            this.errors = [];
            this.messages = [];

            if (column.changed != undefined && column.changed) { 
                this.loading = true;

                axios
                    .put(column.update_path, column)
                    .then(response => {
                        this.loading = false;

                        if(response.data != undefined && response.data.message != undefined) {
                            if(typeof response.data.message == "string"){
                                this.messages.push(response.data.message);
                            } else {
                                this.messages = response.data.message;
                            }
                        }

                        this.fetchData(this.getColumnsPath);
                    }).catch(error => {
                        if(error.response != undefined) {
                            if(error.response.data.errors != undefined) {
                                if(typeof error.response.data.errors == "string"){
                                    this.errors.push(error.response.data.errors);
                                } else {
                                    this.errors = error.response.data.errors;
                                }
                            }
                            
                            if(error.response.data.message != undefined) {
                                if(typeof error.response.data.message == "string"){
                                    this.errors.push(error.response.data.message);
                                } else {
                                    this.errors = error.response.data.message;
                                }
                            }
                        }

                        this.loading = false;
                    });
            }
        },

        toggleAddNewCard (column, index) {
            this.currentList.forEach((e, idx) => {
                if (e.add_new_card) {
                    e.add_new_card = false;
                }
            });

            if (column.add_new_card != undefined) {
                column.add_new_card = !column.add_new_card;
            } else {
                column.add_new_card = true;
            }

            this.$nextTick(() => {
                this.$refs.newCard[index].focus()
            });
        },

        saveCard(column) {
            this.loading = true;
            this.errors = [];
            this.messages = [];
            this.newCard.column_id = column.id;

            axios
                .post(this.saveNewCardPath, this.newCard)
                .then(response => {
                    this.loading = false;

                    if(response.data != undefined && response.data.message != undefined) {
                        if(typeof response.data.message == "string"){
                            this.messages.push(response.data.message);
                        } else {
                            this.messages = response.data.message;
                        }
                    }

                    column.add_new_card = false;
                    this.newCard = {
                        column_id: null,
                        title: '',
                        description: ''
                    };
                    column.cards = response.data.cards;
                }).catch(error => {
                    if(error.response != undefined) {
                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }
                        
                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
            });
        },

        openCardModal(card) {
            this.$vfm.show('cardModal-' + card.id);
        },

        closeModal(card) {
            this.fetchData(this.getColumnsPath);
        },

        updateCard(card) {
            this.errors = [];
            this.messages = [];
            this.loading = true;

            axios
                .put(card.update_path, card)
                .then(response => {
                    this.loading = false;

                    if(response.data != undefined && response.data.message != undefined) {
                        if(typeof response.data.message == "string"){
                            this.messages.push(response.data.message);
                        } else {
                            this.messages = response.data.message;
                        }
                    }

                    this.fetchData(this.getColumnsPath);
                }).catch(error => {
                    if(error.response != undefined) {
                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }
                        
                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
                });
        },

        deleteCard(card) {
            this.loading = true;
            this.errors = [];
            this.messages = [];

            axios
                .delete(card.delete_path, card)
                .then(response => {
                    this.loading = false;

                    if(response.data != undefined && response.data.message != undefined) {
                        if(typeof response.data.message == "string"){
                            this.messages.push(response.data.message);
                        } else {
                            this.messages = response.data.message;
                        }
                    }

                    this.fetchData(this.getColumnsPath);
                }).catch(error => {
                    if(error.response != undefined) {
                        if(error.response.data.errors != undefined) {
                            if(typeof error.response.data.errors == "string"){
                                this.errors.push(error.response.data.errors);
                            } else {
                                this.errors = error.response.data.errors;
                            }
                        }
                        
                        if(error.response.data.message != undefined) {
                            if(typeof error.response.data.message == "string"){
                                this.errors.push(error.response.data.message);
                            } else {
                                this.errors = error.response.data.message;
                            }
                        }
                    }

                    this.loading = false;
            });
        },

        updateOrder: function(evt) {
            if (evt.moved) {
                let columnIndex = this.currentList.map(function(e) {
                    return e.id;
                }).indexOf(evt.moved.element.column_id);

                if (columnIndex !== -1) {
                    let column = this.currentList[columnIndex];

                    column.cards.forEach((card, index) => {
                        card.order = index;

                        axios.put(card.update_path, card)
                            .catch(error => {
                                if(error.response != undefined) {
                                    if(error.response.data.errors != undefined) {
                                        if(typeof error.response.data.errors == "string"){
                                            this.errors.push(error.response.data.errors);
                                        } else {
                                            this.errors = error.response.data.errors;
                                        }
                                    }
                                    
                                    if(error.response.data.message != undefined) {
                                        if(typeof error.response.data.message == "string"){
                                            this.errors.push(error.response.data.message);
                                        } else {
                                            this.errors = error.response.data.message;
                                        }
                                    }
                                }

                            });
                    });
                }
            }

            if (evt.removed && this.oldColumnID) {
                let columnIndex = this.currentList.map(function(e) {
                    return e.id;
                }).indexOf(this.oldColumnID);

                if (columnIndex !== -1) {
                    let column = this.currentList[columnIndex];

                    column.cards.forEach((card, index) => {
                        card.order = index;

                        axios.put(card.update_path, card)
                            .catch(error => {
                                if(error.response != undefined) {
                                    if(error.response.data.errors != undefined) {
                                        if(typeof error.response.data.errors == "string"){
                                            this.errors.push(error.response.data.errors);
                                        } else {
                                            this.errors = error.response.data.errors;
                                        }
                                    }
                                    
                                    if(error.response.data.message != undefined) {
                                        if(typeof error.response.data.message == "string"){
                                            this.errors.push(error.response.data.message);
                                        } else {
                                            this.errors = error.response.data.message;
                                        }
                                    }
                                }

                            });
                    });
                }
            }

            if (evt.added) {
                let column = this.currentList.find(column => column.cards.some(item => item.id === evt.added.element.id));

                column.cards.forEach((card, index) => {
                    if (card.column_id != column.id) {
                        this.oldColumnID = card.column_id;
                    }
                    card.order = index;
                    card.column_id = column.id;

                    axios.put(card.update_path, card)
                        .catch(error => {
                            if(error.response != undefined) {
                                if(error.response.data.errors != undefined) {
                                    if(typeof error.response.data.errors == "string"){
                                        this.errors.push(error.response.data.errors);
                                    } else {
                                        this.errors = error.response.data.errors;
                                    }
                                }
                                
                                if(error.response.data.message != undefined) {
                                    if(typeof error.response.data.message == "string"){
                                        this.errors.push(error.response.data.message);
                                    } else {
                                        this.errors = error.response.data.message;
                                    }
                                }
                            }

                        });
                });
            }
        },
    }
}
</script>
<style scoped>
    ::v-deep .modal-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    ::v-deep .modal-content {
        display: flex;
        flex-direction: column;
        margin: 0 1rem;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        background: #fff;
    }

    ::v-deep form {
        position: relative;
    }

    .modal__title {
        font-size: 1rem;
        margin-bottom: 1rem;;
        font-weight: 700;
    }

    .dark-mode div::v-deep .modal-content {
        border-color: #2d3748;
        background-color: #1a202c;
    }
</style>
