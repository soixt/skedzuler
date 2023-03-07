<template>
    <div class="calendar">
        <div class="title">
            <span class="navigation" @click="navigate('prev')"><<</span>
            <span class="month">{{ monthAsString }}</span>
            <span class="year">{{ currentDate.year }}</span>
            <span class="navigation" @click="navigate('next')">>></span>
        </div>
        <div class="week-days">
            <b v-for="(day, i) in weekDays" :key="i">{{ day }}</b>
        </div>
        <div class="days">
            <template v-for="(c, i) in showCalendarDays">
                <button v-if="!c.event" :key="i" @click.exact="addEventForm(c.day)" :disabled="c.disabled">{{ c.day }}</button>
                <button v-if="c.event" :key="i" @click.exact="showEvent(c)" :disabled="c.disabled">
                    {{ c.day }}
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 8 8"><path d="M0 0v2h7V0H0zm0 3v4.91c0 .05.04.09.09.09H6.9c.05 0 .09-.04.09-.09V3h-7zm1 1h1v1H1V4zm2 0h1v1H3V4zm2 0h1v1H5V4zM1 6h1v1H1V6zm2 0h1v1H3V6z" fill="#626262"/></svg>
                </button>
            </template>
        </div>
        <div class="modal is-active" v-if="modal == 'add-event'">
            <div class="modal-background" @click="close"></div>
            <div class="modal-content">
                <div class="modal-card has-text-centered">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Add Event</p>
                        <button class="delete" @click="close" aria-label="close"></button>
                    </header>
                    <section class="modal-card-body">
                        <div class="field">
                            <div class="control">
                                <textarea class="textarea is-primary" resize="none" v-model="newEventForm.description" placeholder="Add description for your event"></textarea>
                            </div>
                            <p class="help is-danger" v-if="newEventForm.errors && newEventForm.errors.description">{{ newEventForm.errors.description[0] }}</p>
                        </div>
                        <button class="button is-primary is-fullwidth" @click="addEvent">Add</button>
                    </section>
                </div>
            </div>
        </div>
        <div class="modal is-active" v-if="modal == 'update-event'">
            <div class="modal-background" @click="close"></div>
            <div class="modal-content">
                <div class="modal-card has-text-centered">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Update Event</p>
                        <button class="delete" @click="close" aria-label="close"></button>
                    </header>
                    <section class="modal-card-body">
                        <div class="field">
                            <div class="control">
                                <textarea class="textarea is-primary" resize="none" v-model="editEventForm.description" placeholder="Add description for your event"></textarea>
                            </div>
                            <p class="help is-danger" v-if="editEventForm.errors && editEventForm.errors.description">{{ editEventForm.errors.description[0] }}</p>
                        </div>
                        <button class="button is-primary is-fullwidth" @click="updateEvent">Update</button>
                    </section>
                </div>
            </div>
        </div>
        <div class="modal is-active" v-if="modal == 'show-event'">
            <div class="modal-background" @click="close"></div>
            <div class="modal-content">
                <div class="modal-card has-text-centered">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Event</p>
                        <button class="delete" @click="close" aria-label="close"></button>
                    </header>
                    <section class="modal-card-body">
                        <p class="mb-4 is-size-3">{{ showEventForm.event }}</p>
                        <button class="button is-primary" @click="updateEventForm(showEventForm)">Edit Event</button>
                        <button class="button is-danger" @click="deleteEvent(showEventForm.id)">Delete Event</button>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
    data () {
        return {
            monthNames: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            weekDays: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            currentDate: {
                year: null,
                month: null,
                day: null
            },
            calendarDays: {
                prevMonthDays: [],
                currentMonthDays: [],
                nextMonthDays: []
            },
            newEventForm: {
                description: '',
                errors: null
            },
            editEventForm: {
                id: null,
                description: '',
                errors: null
            },
            showEventForm: null,
            modal: ''
        }
    },
    methods: {
        navigate (direction) {
            let month = this.monthAsString;
            let year = this.currentDate.year;
            let index = this.monthNames.findIndex(function (m) {
                return m == month
            })
            if (direction == "prev") {
                if (index == 0) {
                    month = this.monthNames[this.monthNames.length - 1]
                    year = year - 1
                } else {
                    month = this.monthNames[index - 1]
                }
            } else if (direction == "next") {
                if (index == 11) {
                    month = this.monthNames[0]
                    year = year + 1
                } else {
                    month = this.monthNames[index + 1]
                }
            }
            this.getEvents(new Date(`01 ${month} ${year}`));
        },
        getLeadingDays (date) {
            const ret = [];
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstWeekday = new Date(year, month, 0).getDay();
            const days = (firstWeekday + 7) - 7 - 1;
            for (let i = days * -1; i <= 0; i++) {
                ret.push({ day: new Date(year, month, i).getDate(), event: null, disabled: true });
            }
            return ret;
        },
        getMonthDays(date, events) {
            const ret = [];
            const year = date.getFullYear();
            const month = date.getMonth();
            const lastDay = new Date(year, month+1, 0).getDate();
            for (let i = 1; i <= lastDay; i++) {
                let event = null;
                let id = null;
                events.forEach(e => {
                    if ((new Date(e.date)).getDate() == i) {
                        event = e.description
                        id = e.id
                    }
                })
                ret.push({ id: id, day: i, event: event, disabled: false });
            }
            return ret;
        },
        getTrailingDays(leadingDays, monthDays) {
            const ret = [];
            let totaldays = leadingDays.length >= 5 ? 42 : 35;
            const days = totaldays - (leadingDays.length + monthDays.length);
            for (let i = 1; i <= days; i++) ret.push({ day: i, event: null, disabled: true });
            return ret;
        },
        showCalendar (date, events) {
            this.calendarDays.prevMonthDays = this.getLeadingDays(date);
            this.calendarDays.currentMonthDays = this.getMonthDays(date, events);
            this.calendarDays.nextMonthDays = this.getTrailingDays(this.calendarDays.prevMonthDays, this.calendarDays.currentMonthDays);
            this.currentDate.year = date.getFullYear();
            this.currentDate.month = date.getMonth() + 1;
        },
        addEventForm (day) {
            this.currentDate.day = day;
            this.modal = 'add-event';
        },
        updateEventForm (event) {
            this.editEventForm.id = event.id
            this.editEventForm.description = event.event
            this.modal = 'update-event';
        },
        addEvent () {
            if (this.newEventForm.description == "") { alert('Description cannot be empty!'); return; }
            axios.post('http://127.0.0.1:8000/api/schedule', {
                date: `${this.currentDate.day}/${this.currentDate.month}/${this.currentDate.year}`,
                description: this.newEventForm.description
            },
            { headers: {"Authorization" : this.getAuth.token }
            }).then(response => {
                this.calendarDays.currentMonthDays.forEach(date => {
                    if (date.day == this.currentDate.day) {
                        date.event = response.data.data.description
                        date.id = response.data.data.id
                    }
                });
                alert('Event added successfully!');
                this.close();
            }).catch(error => {
                this.newEventForm.errors = error.response.data.errors
            });
        },
        updateEvent () {
            if (this.editEventForm.description == "") { alert('Description cannot be empty!'); return; }
            axios.post('http://127.0.0.1:8000/api/schedule/edit', {
                description: this.editEventForm.description,
                id: this.editEventForm.id
            },
            { headers: {"Authorization" : this.getAuth.token }
            }).then(response => {
                this.calendarDays.currentMonthDays.forEach(date => {
                    if (date.id == response.data.data.id) {
                        date.event = response.data.data.description
                    }
                });
                alert('Event updated successfully!');
                this.close();
                this.modal = 'show-event'
            }).catch(error => {
                this.editEventForm.errors = error.response.data.errors
            });
        },
        deleteEvent (id) {
            if (!id) { alert('Something went wrong!'); return; }
            axios.post('http://127.0.0.1:8000/api/schedule/delete', {
                id: id
            }, {
                headers: {"Authorization" : this.getAuth.token }
            }).then(response => {
                this.calendarDays.currentMonthDays.forEach(date => {
                    if (date.id == id) {
                        date.event = null
                    }
                });
                alert(response.data.message);
                this.close();
            }).catch(error => {
                alert('Something went wrong!')
            });
            
        },
        showEvent (event) {
            this.showEventForm = event
            this.modal = 'show-event'
        },
        close () {
            this.newEventForm.description = ''
            this.updateEventForm.description = ''
            this.modal = ''
        },
        getEvents (date) {
            axios.post('http://127.0.0.1:8000/api/schedules', {
                date: `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`
            }).then(response => {
                this.showCalendar(date, response.data.data);
            });
        }
    },
    mounted () {
        this.getEvents(new Date())
    },
    computed: {
        ...mapGetters(['getAuth']),
        monthAsString () {
            return this.monthNames[this.currentDate.month - 1];
        },
        showCalendarDays () {
            return [...this.calendarDays.prevMonthDays, ...this.calendarDays.currentMonthDays, ...this.calendarDays.nextMonthDays];
        }
    }
}
</script>

<style scoped>
* {
    box-sizing: border-box;
}
.textarea {
    resize: none
}
.title {font-size: 2rem;}
.calendar {width: calc(100% - 100px); padding: 50px; text-align: center}
.calendar .week-days b,
.calendar .days button { 
  display: block;
  float: left;
  width: calc(100% / 7);
  height: 50px;
}
.new-event {
    width: 400px;
    background-color: #fff;
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    position: fixed;
    top: 50px;
    left: calc(50% - 200px);
    overflow-x: hidden;
}
</style>