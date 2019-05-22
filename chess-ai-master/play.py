"""Main entry point into the game"""

import json
import sys
from chessgame import ChessGame

if __name__ == '__main__':

    cont = True
    invalidPQ = []
    payload = json.loads(sys.argv[1])
    while cont:
        current_game = ChessGame(payload, invalidPQ)
        json_data = current_game.make_move_ai(payload['level'])
        if json_data[0] == '{':
            print(json_data)
            cont = False
        else:
            invalidPQ = json_data