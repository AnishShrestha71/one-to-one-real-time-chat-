<template>
    <div class="w-3/4 mx-auto">
        <div class="h-full  flex " v-for="message in allMessages" :key="message.id" :class="
                    user.id === message.user_id ? 'justify-end' : 'justify-start'
                "
                >
            <div
                class=" mb-8"
                
                v-if="message.message"
            >
                {{ message.message }}
            </div>

            <img
                v-if="message.image"
                :src="'/images/' + message.image"
                class="w-24 h-24 "
            />
        </div>
        <div>
            <input
                type="text"
                v-model="message"
                placeholder="write a message"
            />
            <button @click="sendMessage">send</button>
        </div>
    </div>
</template>
<script>
export default {
    props: ["user"],
    data() {
        return {
            message: null,
            allMessages: []
        };
    },
    methods: {
        sendMessage() {
            if (!this.message) {
                return alert("type message");
            }

            axios
                .post("/send_message", { message: this.message })
                .then(response => {
                    this.message = "";
                    this.fetchMessage();
                    console.log("message.sent");
                });
        },
        fetchMessage() {
            axios.get("/recieve_message").then(response => {
                this.allMessages = response.data;
                console.log(this.allMessages);
            });
        }
    },
    mounted() {
        Echo.private("chat").listen("MessageSent", e => {
            this.allMessages.push(e.message);
        });
        console.log(this.user);
    },
    created() {
        this.fetchMessage();
    }
};
</script>
