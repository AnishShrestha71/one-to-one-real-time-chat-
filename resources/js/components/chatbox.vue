<template>
    <div class="w-3/4 mx-auto">
        <div>
      
        </div>
        <div class="h-full" v-for="message in allMessages" :key="message.id">
            <div class=" mb-8" :class="user.id === message.user_id ? 'text-right' : 'text-left' ">
               {{message.message}}
            </div>
           
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
    props:[
        'user'
    ],
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
                    this.message = '';
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
    mounted(){
        Echo.private('chat').listen('MessageSent',(e)=>
        {
            this.allMessages.push(e.message)
        })
        console.log(this.user)
    },
    created(){
        this.fetchMessage();
    }
};
</script>
