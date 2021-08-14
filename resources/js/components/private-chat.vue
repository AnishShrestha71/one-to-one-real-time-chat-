<template>
    <div class="flex">
        <div class="w-1/4">
            <div v-for="reciveruser in users" :key="reciveruser.id">
                <div @click="activeFriend = reciveruser.id"  :class="activeFriend === reciveruser.id ? 'text-blue-800' : '' ">
                    {{ reciveruser.name }}
                </div>
            </div>
        </div>
        <div v-if="activeFriend != null">
            <div>Person name</div>
            <div>
                <div
                    class="h-full"
                    v-for="message in allMessages"
                    :key="message.id"
                >
                    <div
                        class=" mb-8"
                        :class="
                            user.id === message.user_id
                                ? 'text-right'
                                : 'text-left'
                        "
                       
                    >
                        {{ message.message }}
                    </div>
                </div>
            </div>
            <div>
                <input
                    type="text"
                    placeholder="type message"
                    v-model="message"
                />
                <button @click="sendMessage">send</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["user"],
    data() {
        return {
            users: [],
            allMessages: [],
            message: "",
            activeFriend: null
        };
    },
    watch: {
        activeFriend(val) {
            this.fetchMessage();
        }
    },
    methods: {
        sendMessage() {
            if (!this.message) {
                return alert("type message");
            }

            axios
                .post(`/send_private_message/${this.activeFriend}`, {
                    message: this.message
                })
                .then(response => {
                    this.message = "";
                    this.fetchMessage(this.activeFriend);
                    console.log("message.sent");
                });
        },
        fetchUsers() {
            axios.get("/users").then(response => {
                this.users = response.data;
            });
        },
        fetchMessage() {
            axios
                .get(`/fetch_private_message/${this.activeFriend}`)
                .then(response => {
                    this.allMessages = response.data;
                });
        }
    },
    mounted() {
        this.fetchUsers();
        Echo.private("pchat."+this.user.id).listen("PrivateMessageSent", e => {
            this.activeFriend = e.message.user_id
            this.allMessages.push(e.message);
        });
    }
};
</script>
