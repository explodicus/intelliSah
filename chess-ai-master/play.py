"""Main entry point into the game"""

import json
from chessgame import ChessGame

if __name__ == '__main__':

    cont = True
    invalidPQ = []
    while cont:
        current_game = ChessGame(3, invalidPQ)
        json_data = current_game.make_move_ai(3)
        if json_data[0] == '{':
            print(json_data)
            cont = False
        else:
            invalidPQ = json_data