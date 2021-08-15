<template>
    <div class="flex">
        <div class="w-1/4">
            <div v-for="reciveruser in users" :key="reciveruser.id">
                <div @click="activeFriend = reciveruser.id">
                    <div class="flex">
                        <div
                            class="text-5xl absolute -mt-6 "
                            :class="
                                onlineFriends.find(
                                    user => user.id === reciveruser.id
                                )
                                    ? 'text-yellow-300'
                                    : 'text-purple-500'
                            "
                        >
                            .
                        </div>
                        <div
                            class="text-2xl ml-8"
                            :class="
                                activeFriend === reciveruser.id
                                    ? 'text-blue-800'
                                    : ''
                            "
                        >
                            {{ reciveruser.name }}
                        </div>
                    </div>
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
                    <div v-if="message.message"
                        class=" mb-8"
                        :class="
                            user.id === message.user_id
                                ? 'text-right'
                                : 'text-left'
                        "
                    >
                        {{ message.message }}
                    </div>
                    <div class="w-24 h-24" v-if="message.image">
                        <img :src="'/images/'+ message.image"/>
                    </div>
                </div>
                <p v-if="typingFriend.name">
                    {{ typingFriend.name }} is typing....
                </p>
            </div>
            <div>
                <input
                    type="text"
                    placeholder="type message"
                    v-model="message"
                    @keydown="typingNotification"
                />
                <button @click="sendMessage">send</button>
                <input type="file" accept="image/jpg" @change="uploadImage"/>
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
            activeFriend: null,
            typingFriend: {},
            typingClock: null,
            onlineFriends: [],
            files:[]
        };
    },
    watch: {
        activeFriend(val) {
            this.fetchMessage();
        },
        files:{
        deep:true,
        handler(){
          let success=this.files[0].success;
          if(success){
            this.fetchMessages();
          }
        }
      },
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
        },
        typingNotification() {
            Echo.private("pchat." + this.activeFriend).whisper("typing", {
                typinguser: this.user
            });
        },
        uploadImage(e) {
            let data = new FormData();
            data.append("name", "mypicture");
            data.append("file", e.target.files[0]);
            axios.post(`/send_private_message/${this.activeFriend}`,data).then(response =>{
                this.fetchMessage();
            });
                
        }
    },
    created() {
        this.fetchUsers();
        Echo.join("activeStatus")
            .here(users => {
                console.log("online", users);
                this.onlineFriends = users;
            })
            .joining(user => {
                this.onlineFriends.push(user);
                console.log("joining", user.name);
            })
            .leaving(user => {
                this.onlineFriends.splice(this.onlineFriends.indexOf(user), 1);
                console.log("leaving", user.name);
            });
        Echo.private("pchat." + this.user.id)

            .listen("PrivateMessageSent", e => {
                this.activeFriend = e.message.user_id;
                this.allMessages.push(e.message);
            })
            .listenForWhisper("typing", e => {
                if (e.typinguser.id == this.activeFriend) {
                    this.typingFriend = e.typinguser;

                    if (this.typingClock) clearTimeout();
                    this.typingClock = setTimeout(() => {
                        this.typingFriend = {};
                    }, 3000);
                }
            });
    }
};
</script>
