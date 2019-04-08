<template>
    <div class="styledGame">
        <div v-show="gameover"><span class="text-success mr-1">Game Over!</span></div>
        <div v-for="(row, keyRow) in gameTable" class="row">
            <div v-for="(col, keyCol) in row" v-on:click=""
                    :class="col.class" @click="selectedSq(col)">
                <template v-if="col.piece"> <img :src="'/images/s' + session.subscriptions[col.piece.subscription_id].side + '_' + col.piece.code + '.png'" > </template>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            gameTable: {},
            gameover: false,
        }),

        props: {
            session: {
                type: Object,
            },
            currentSubscription: {
                type: Object,
            },
            handleUri: {
                type: String,
            }
        },

        mounted() {
            Echo.private(`sah.game.${this.session.id}`)
                .listen('GameEvent', (e) => {
                    if (!this.session.current_subscription_id) {
                        location.reload();
                    }
                    this.gameover = e.session.gameover;
                    this.session.game_bag = e.session.game_bag;
                    this.session.current_subscription_id = e.session.current_subscription_id;
                    this.updateGameTable();
                    if (!this.gameover) {
                        VueEvents.$emit('notification', {
                            text: `User '${this.session.subscribers[this.session.subscriptions[this.session.current_subscription_id].user_id].name}' playing now.`
                        });
                    } else {
                        VueEvents.$emit('notification', {
                            text: `User '${this.session.subscribers[this.session.subscriptions[this.session.current_subscription_id].user_id].name}' won the game.`
                        });
                    }
                });

            this.updateGameTable();
            this.mapSubscribers();
        },

        methods: {
            mapSubscribers() {
                this.session.subscriptions = this.session.subscriptions.reduce((carry, subscription) => {
                    carry[subscription.id] = subscription;

                    return carry;
                }, {});

                this.session.subscribers = this.session.subscribers.reduce((carry, subscriber) => {
                    carry[subscriber.id] = subscriber;

                    return carry;
                }, {});
            },

            updateGameTable() {
                let squareClasses = {};
                for (let row = 0; row < 12; row++) {
                    squareClasses[row] = {};
                    for (let column = 0; column < 12; column++) {
                        if ((column < 2 && row < 2) || (column < 2 && row > 9) ||
                            (column > 9 && row < 2) || (column > 9 && row > 9)) {
                            squareClasses[row][column] = {class: ["square", "hidden"]};
                        }
                        else if ((column % 2 == 1 && row % 2 == 1) || (column % 2 == 0 && row % 2 == 0)) {
                            squareClasses[row][column] = {class: ["square", "light"]};
                        }
                        else if ((column % 2 == 0 && row % 2 == 1) || (column % 2 == 1 && row % 2 == 0)) {
                            squareClasses[row][column] = {class: ["square", "dark"]};
                        }

                        try {
                            squareClasses[row][column].piece = JSON.parse(this.session.game_bag[row][column]);
                        } catch (e) {

                        }
                    }
                }

                this.gameTable = squareClasses;
                for (let row = 0; row < 12; row++) {
                    for (let column = 0; column < 12; column++) {
                        this.gameTable[row][column].noPiece = [row, column];
                    }
                }

                if (this.currentSubscription.side == 4) {
                    let n = 12;
                    for (let i = 0; i < n / 2; i++) {
                        for (let j = i; j < n - i - 1; j++) {
                            let tmp = this.gameTable[i][j];
                            this.gameTable[i][j] = this.gameTable[j][n - i - 1];
                            this.gameTable[j][n - i - 1] = this.gameTable[n - i - 1][n - j - 1];
                            this.gameTable[n - i - 1][n - j - 1] = this.gameTable[n - j - 1][i];
                            this.gameTable[n - j - 1][i] = tmp;
                        }
                    }
                }
                else if (this.currentSubscription.side == 2) {
                    let n = 12;
                    for (let i = 0; i < n / 2; i++) {
                        for (let j = i; j < n - i - 1; j++) {
                            let tmp = this.gameTable[i][j];
                            this.gameTable[i][j] = this.gameTable[n - j - 1][i];;
                            this.gameTable[n - j - 1][i] = this.gameTable[n - i - 1][n - j - 1];
                            this.gameTable[n - i - 1][n - j - 1] = this.gameTable[j][n - i - 1];
                            this.gameTable[j][n - i - 1] = tmp;
                        }
                    }
                }
                else if (this.currentSubscription.side == 1) {
                    let n = 12;
                    for (let i = 0; i < n / 2; i++) {
                        for (let j = i; j < n - i - 1; j++) {
                            let tmp = this.gameTable[i][j];
                            this.gameTable[i][j] = this.gameTable[n - j - 1][i];
                            this.gameTable[n - j - 1][i] = this.gameTable[n - i - 1][n - j - 1];
                            this.gameTable[n - i - 1][n - j - 1] = this.gameTable[j][n - i - 1];
                            this.gameTable[j][n - i - 1] = tmp;
                        }
                    }
                    for (let i = 0; i < n / 2; i++) {
                        for (let j = i; j < n - i - 1; j++) {
                            let tmp = this.gameTable[i][j];
                            this.gameTable[i][j] = this.gameTable[n - j - 1][i];
                            this.gameTable[n - j - 1][i] = this.gameTable[n - i - 1][n - j - 1];
                            this.gameTable[n - i - 1][n - j - 1] = this.gameTable[j][n - i - 1];
                            this.gameTable[j][n - i - 1] = tmp;
                        }
                    }
                }
            },

            selectedSq(col) {
                if (this.session.current_subscription_id == this.currentSubscription.id) {
                    let isPossible = 0;
                    if (col.class.indexOf("sqPossible") != -1) {
                        isPossible = 1;
                    }
                    if (isPossible) {
                        for (let row = 0; row < 12; row++) {
                            for (let column = 0; column < 12; column++) {
                                if (this.gameTable[row][column].class.indexOf("sqSelected") != -1) {
                                    axios.post(this.handleUri, {
                                        'position_from': this.gameTable[row][column].piece.position,
                                        'position_to': col.noPiece,
                                    });
                                }
                            }
                        }
                    }
                    if (col.piece && col.piece.subscription_id == this.currentSubscription.id) {
                        for (let row = 0; row < 12; row++) {
                            for (let column = 0; column < 12; column++) {
                                let index = this.gameTable[row][column].class.indexOf("sqSelected");
                                if (index != -1) {
                                    this.gameTable[row][column].class.splice(index, 1);
                                }
                                index = this.gameTable[row][column].class.indexOf("sqPossible");
                                if (index != -1) {
                                    this.gameTable[row][column].class.splice(index, 1);
                                    index = this.gameTable[row][column].class.indexOf("attacked");
                                    if (index != -1) {
                                        this.gameTable[row][column].class.splice(index, 1);
                                    }
                                }
                            }
                        }
                        col.class.push("sqSelected");
                        let relativeX;
                        let relativeY;
                        for (let row = 0; row < 12; row++) {
                            for (let column = 0; column < 12; column++) {
                                if (this.gameTable[row][column].piece &&
                                    this.gameTable[row][column].piece.position[0] == col.piece.position[0] &&
                                    this.gameTable[row][column].piece.position[1] == col.piece.position[1]) {
                                    relativeX = column;
                                    relativeY = row;;
                                    break;
                                }
                            }
                        }
                        if (col.piece.code == "pawn") {
                            if (!this.gameTable[8][relativeX].piece &&
                                !this.gameTable[9][relativeX].piece &&
                                relativeY == 10) {
                                this.gameTable[8][relativeX].class.push("sqPossible");
                            }
                            if (relativeY - 1 >= 0 && relativeX - 1 >= 0 &&
                                this.gameTable[relativeY - 1][relativeX - 1].piece) {
                                this.gameTable[relativeY - 1][relativeX - 1].class.push("sqPossible");
                                this.gameTable[relativeY - 1][relativeX - 1].class.push("attacked");
                            }
                            if (relativeY - 1 >= 0 && relativeX + 1 < 12 &&
                                this.gameTable[relativeY - 1][relativeX + 1].piece) {
                                this.gameTable[relativeY - 1][relativeX + 1].class.push("sqPossible");
                                this.gameTable[relativeY - 1][relativeX + 1].class.push("attacked");
                            }
                            if (relativeY - 1 >= 0 && !this.gameTable[relativeY - 1][relativeX].piece) {
                                this.gameTable[relativeY - 1][relativeX].class.push("sqPossible");
                            }
                        }
                        else if (col.piece.code == "rook") {
                            let index = relativeY - 1;
                            while (index >= 0 && !this.gameTable[index][relativeX].piece) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                index--;
                            }
                            if (index >= 0) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                this.gameTable[index][relativeX].class.push("attacked");
                            }

                            index = relativeY + 1;
                            while (index < 12 && !this.gameTable[index][relativeX].piece) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                index++;
                            }
                            if (index < 12) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                this.gameTable[index][relativeX].class.push("attacked");
                            }

                            index = relativeX - 1;
                            while (index >= 0 && !this.gameTable[relativeY][index].piece) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                index--;
                            }
                            if (index >= 0) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                this.gameTable[relativeY][index].class.push("attacked");
                            }

                            index = relativeX + 1;
                            while (index < 12 && !this.gameTable[relativeY][index].piece) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                index++;
                            }
                            if (index < 12) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                this.gameTable[relativeY][index].class.push("attacked");
                            }
                        }
                        else if (col.piece.code == "bishop") {
                            let indexX = relativeX + 1;
                            let indexY = relativeY + 1
                            while (indexX < 12 && indexY < 12 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX++;
                                indexY++;
                            }
                            if (indexX < 12 && indexY < 12) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }
                            indexX = relativeX - 1;
                            indexY = relativeY + 1
                            while (indexX >= 0 && indexY < 12 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX--;
                                indexY++;
                            }
                            if (indexX >= 0 && indexY < 12) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }
                            indexX = relativeX + 1;
                            indexY = relativeY - 1
                            while (indexX < 12 && indexY >= 0 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX++;
                                indexY--;
                            }
                            if (indexX < 12 && indexY >= 0) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }
                            indexX = relativeX - 1;
                            indexY = relativeY - 1
                            while (indexX >= 0 && indexY >= 0 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX--;
                                indexY--;
                            }
                            if (indexX >= 0 && indexY >= 0) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }
                        }
                        else if (col.piece.code == "queen") {
                            let indexX = relativeX + 1;
                            let indexY = relativeY + 1
                            while (indexX < 12 && indexY < 12 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX++;
                                indexY++;
                            }
                            if (indexX < 12 && indexY < 12) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }

                            indexX = relativeX - 1;
                            indexY = relativeY + 1
                            while (indexX >= 0 && indexY < 12 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX--;
                                indexY++;
                            }
                            if (indexX >= 0 && indexY < 12) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }

                            indexX = relativeX + 1;
                            indexY = relativeY - 1
                            while (indexX < 12 && indexY >= 0 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX++;
                                indexY--;
                            }
                            if (indexX < 12 && indexY >= 0) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }

                            indexX = relativeX - 1;
                            indexY = relativeY - 1
                            while (indexX >= 0 && indexY >= 0 && !this.gameTable[indexY][indexX].piece) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                indexX--;
                                indexY--;
                            }
                            if (indexX >= 0 && indexY >= 0) {
                                this.gameTable[indexY][indexX].class.push("sqPossible");
                                this.gameTable[indexY][indexX].class.push("attacked");
                            }

                            let index = relativeY - 1;
                            while (index >= 0 && !this.gameTable[index][relativeX].piece) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                index--;
                            }
                            if (index >= 0) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                this.gameTable[index][relativeX].class.push("attacked");
                            }

                            index = relativeY + 1;
                            while (index < 12 && !this.gameTable[index][relativeX].piece) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                index++;
                            }
                            if (index < 12) {
                                this.gameTable[index][relativeX].class.push("sqPossible");
                                this.gameTable[index][relativeX].class.push("attacked");
                            }

                            index = relativeX - 1;
                            while (index >= 0 && !this.gameTable[relativeY][index].piece) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                index--;
                            }
                            if (index >= 0) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                this.gameTable[relativeY][index].class.push("attacked");
                            }

                            index = relativeX + 1;
                            while (index < 12 && !this.gameTable[relativeY][index].piece) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                index++;
                            }
                            if (index < 12) {
                                this.gameTable[relativeY][index].class.push("sqPossible");
                                this.gameTable[relativeY][index].class.push("attacked");
                            }
                        }
                        else if (col.piece.code == "king") {
                            if (relativeX - 1 >= 0) {
                                this.gameTable[relativeY][relativeX - 1].class.push("sqPossible");
                                if (this.gameTable[relativeY][relativeX - 1].piece) {
                                    this.gameTable[relativeY][relativeX - 1].class.push("attacked");
                                }
                            }
                            if (relativeX + 1 < 12) {
                                this.gameTable[relativeY][relativeX + 1].class.push("sqPossible");
                                if (this.gameTable[relativeY][relativeX + 1].piece) {
                                    this.gameTable[relativeY][relativeX + 1].class.push("attacked");
                                }
                            }
                            if (relativeY - 1 >= 0) {
                                this.gameTable[relativeY - 1][relativeX].class.push("sqPossible");
                                if (this.gameTable[relativeY - 1][relativeX].piece) {
                                    this.gameTable[relativeY - 1][relativeX].class.push("attacked");
                                }
                            }
                            if (relativeY + 1 < 12) {
                                this.gameTable[relativeY + 1][relativeX].class.push("sqPossible");
                                if (this.gameTable[relativeY + 1][relativeX].piece) {
                                    this.gameTable[relativeY + 1][relativeX].class.push("attacked");
                                }
                            }
                            if (relativeY - 1 >= 0 && relativeX - 1 >= 0) {
                                this.gameTable[relativeY - 1][relativeX - 1].class.push("sqPossible");
                                if (this.gameTable[relativeY - 1][relativeX - 1].piece) {
                                    this.gameTable[relativeY - 1][relativeX - 1].class.push("attacked");
                                }
                            }
                            if (relativeY - 1 >= 0 && relativeX + 1 < 12) {
                                this.gameTable[relativeY - 1][relativeX + 1].class.push("sqPossible");
                                if (this.gameTable[relativeY - 1][relativeX + 1].piece) {
                                    this.gameTable[relativeY - 1][relativeX + 1].class.push("attacked");
                                }
                            }
                            if (relativeY + 1 < 12 && relativeX - 1 >= 0) {
                                this.gameTable[relativeY + 1][relativeX - 1].class.push("sqPossible");
                                if (this.gameTable[relativeY + 1][relativeX - 1].piece) {
                                    this.gameTable[relativeY + 1][relativeX - 1].class.push("attacked");
                                }
                            }
                            if (relativeY + 1 < 12 && relativeX + 1 < 12) {
                                this.gameTable[relativeY + 1][relativeX + 1].class.push("sqPossible");
                                if (this.gameTable[relativeY + 1][relativeX + 1].piece) {
                                    this.gameTable[relativeY + 1][relativeX + 1].class.push("attacked");
                                }
                            }
                        }
                        else if (col.piece.code == "knight") {
                            if (relativeY + 2 < 12 && relativeX + 1 < 12) {
                                this.gameTable[relativeY + 2][relativeX + 1].class.push("sqPossible");
                                if (this.gameTable[relativeY + 2][relativeX + 1].piece) {
                                    this.gameTable[relativeY + 2][relativeX + 1].class.push("attacked");
                                }
                            }
                            if (relativeY + 1 < 12 && relativeX + 2 < 12) {
                                this.gameTable[relativeY + 1][relativeX + 2].class.push("sqPossible");
                                if (this.gameTable[relativeY + 1][relativeX + 2].piece) {
                                    this.gameTable[relativeY + 1][relativeX + 2].class.push("attacked");
                                }
                            }
                            if (relativeY - 1 >= 0 && relativeX + 2 < 12) {
                                this.gameTable[relativeY - 1][relativeX + 2].class.push("sqPossible");
                                if (this.gameTable[relativeY - 1][relativeX + 2].piece) {
                                    this.gameTable[relativeY - 1][relativeX + 2].class.push("attacked");
                                }
                            }
                            if (relativeY - 2 >= 0 && relativeX + 1 < 12) {
                                this.gameTable[relativeY - 2][relativeX + 1].class.push("sqPossible");
                                if (this.gameTable[relativeY - 2][relativeX + 1].piece) {
                                    this.gameTable[relativeY - 2][relativeX + 1].class.push("attacked");
                                }
                            }
                            if (relativeY - 2 >= 0 && relativeX - 1 >= 0) {
                                this.gameTable[relativeY - 2][relativeX - 1].class.push("sqPossible");
                                if (this.gameTable[relativeY - 2][relativeX - 1].piece) {
                                    this.gameTable[relativeY - 2][relativeX - 1].class.push("attacked");
                                }
                            }
                            if (relativeY - 1 >= 0 && relativeX - 2 >= 0) {
                                this.gameTable[relativeY - 1][relativeX - 2].class.push("sqPossible");
                                if (this.gameTable[relativeY - 1][relativeX - 2].piece) {
                                    this.gameTable[relativeY - 1][relativeX - 2].class.push("attacked");
                                }
                            }
                            if (relativeY + 1 < 12 && relativeX - 2 >= 0) {
                                this.gameTable[relativeY + 1][relativeX - 2].class.push("sqPossible");
                                if (this.gameTable[relativeY + 1][relativeX - 2].piece) {
                                    this.gameTable[relativeY + 1][relativeX - 2].class.push("attacked");
                                }
                            }
                            if (relativeY + 2 < 12 && relativeX - 1 >= 0) {
                                this.gameTable[relativeY + 2][relativeX - 1].class.push("sqPossible");
                                if (this.gameTable[relativeY + 2][relativeX - 1].piece) {
                                    this.gameTable[relativeY + 2][relativeX - 1].class.push("attacked");
                                }
                            }
                        }
                        for (let row = 0; row < 12; row++) {
                            for (let column = 0; column < 12; column++) {
                                if (this.gameTable[row][column].piece &&
                                    this.gameTable[row][column].class.indexOf("attacked") != -1 &&
                                    this.gameTable[row][column].piece.subscription_id == this.currentSubscription.id) {
                                    let index = this.gameTable[row][column].class.indexOf("sqPossible");
                                    this.gameTable[row][column].class.splice(index, 1);
                                    index = this.gameTable[row][column].class.indexOf("attacked");
                                    this.gameTable[row][column].class.splice(index, 1);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</script>
