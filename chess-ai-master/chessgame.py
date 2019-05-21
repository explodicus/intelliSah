"""chess game"""
import math
import itertools
import json
import sys

from game import RulesEnforcer
from ai import ChessAi


class ChessGame(RulesEnforcer,ChessAi):
    def __init__(self, ai_depth, invalidPQ):
        """
        Creates a chessboard with pieces
        
        params:
        ai_depth: max number of moves to search into the future
        
        Notation:
        ------------
        000 == empty space  
        
        "b-p"   == black pawn
        "b-r"   == black rook
        "b-r"   == black rook
        "b-n"   == black knight
        "b-b"   == black bishop
        "b-q"   == black queen
        "b-k"   == black king  
        
        "w-k"   == white king

        ... etc etc you get the idea
        
        
        As soon as the chess game is initialized, the chess computer will start calculating

        """
        
        ChessAi.__init__(self, ai_depth)
        RulesEnforcer.__init__(self)
        #super(ChessGame, self).__init__()

        self.ai_depth = ai_depth

        chessboard1 = [["0-0"]*12 for i in range(12)]

        self.invalidPQ = invalidPQ

        def fill(color, code, position):
            chessboard1[position[0]][position[1]] = color + '-'
            if code == 'rook':
                chessboard1[position[0]][position[1]] += 'r'
            elif code == 'pawn':
                chessboard1[position[0]][position[1]] += 'p'
            elif code == 'knight':
                chessboard1[position[0]][position[1]] += 'n'
            elif code == 'bishop':
                chessboard1[position[0]][position[1]] += 'b'
            elif code == 'queen':
                chessboard1[position[0]][position[1]] += 'q'
            elif code == 'king':
                chessboard1[position[0]][position[1]] += 'k'

        def okPQ(i,j):
            for k in range(len(self.invalidPQ)):
                if i == self.invalidPQ[k][0] and j == self.invalidPQ[k][1]:
                    return False
            return True
        
        #initialize the chessboard
        self.chessboard = [["0-0"]*8 for i in range(8)]

        original = sys.argv[1]
        # original = '[{"9":{"code":"rook","ai":true,"position":[0,9]},"8":{"code":"knight","ai":true,"position":[0,8]},"7":{"code":"bishop","ai":true,"position":[0,7]},"6":{"code":"queen","ai":true,"position":[0,6]},"5":{"code":"king","ai":true,"position":[0,5]},"4":{"code":"bishop","ai":true,"position":[0,4]},"3":{"code":"knight","ai":true,"position":[0,3]},"2":{"code":"rook","ai":true,"position":[0,2]}},{"8":{"code":"pawn","ai":true,"position":[1,8]},"7":{"code":"pawn","ai":true,"position":[1,7]},"6":{"code":"pawn","ai":true,"position":[1,6]},"5":{"code":"pawn","ai":true,"position":[1,5]},"4":{"code":"pawn","ai":true,"position":[1,4]},"3":{"code":"pawn","ai":true,"position":[1,3]},"2":{"code":"pawn","ai":true,"position":[1,2]}},{"0":{"code":"rook","ai":false,"position":[2,0]},"1":{"code":"pawn","ai":false,"position":[2,1]},"11":{"code":"rook","ai":false,"position":[2,11]},"10":{"code":"pawn","ai":true,"position":[2,10]}},{"0":{"code":"knight","ai":false,"position":[3,0]},"1":{"code":"pawn","ai":false,"position":[3,1]},"11":{"code":"knight","ai":false,"position":[3,11]},"10":{"code":"pawn","ai":false,"position":[3,10]}},{"0":{"code":"bishop","ai":false,"position":[4,0]},"1":{"code":"pawn","ai":false,"position":[4,1]},"11":{"code":"bishop","ai":false,"position":[4,11]},"10":{"code":"pawn","ai":false,"position":[4,10]}},{"0":{"code":"queen","ai":false,"position":[5,0]},"1":{"code":"pawn","ai":false,"position":[5,1]},"11":{"code":"queen","ai":false,"position":[5,11]},"10":{"code":"pawn","ai":false,"position":[5,10]}},{"0":{"code":"king","ai":false,"position":[6,0]},"1":{"code":"pawn","ai":false,"position":[6,1]},"11":{"code":"king","ai":false,"position":[6,11]},"10":{"code":"pawn","ai":false,"position":[6,10]}},{"8":{"code":"pawn","ai":false,"position":[7,8]},"3":{"code":"pawn","ai":false,"position":[7,3]},"0":{"code":"bishop","ai":false,"position":[7,0]},"11":{"code":"bishop","ai":false,"position":[7,11]}},{"8":{"code":"pawn","ai":false,"position":[8,8]},"0":{"code":"knight","ai":false,"position":[8,0]},"1":{"code":"pawn","ai":false,"position":[8,1]},"11":{"code":"knight","ai":false,"position":[8,11]},"10":{"code":"pawn","ai":false,"position":[8,10]}},{"0":{"code":"rook","ai":false,"position":[9,0]},"1":{"code":"pawn","ai":false,"position":[9,1]},"11":{"code":"rook","ai":false,"position":[9,11]},"10":{"code":"pawn","ai":false,"position":[9,10]}},{"9":{"code":"pawn","ai":false,"position":[10,9]},"7":{"code":"pawn","ai":false,"position":[10,7]},"6":{"code":"pawn","ai":false,"position":[10,6]},"5":{"code":"pawn","ai":false,"position":[10,5]},"4":{"code":"pawn","ai":false,"position":[10,4]},"3":{"code":"pawn","ai":false,"position":[10,3]},"2":{"code":"pawn","ai":false,"position":[10,2]}},{"9":{"code":"rook","ai":false,"position":[11,9]},"8":{"code":"knight","ai":false,"position":[11,8]},"7":{"code":"bishop","ai":false,"position":[11,7]},"6":{"code":"queen","ai":false,"position":[11,6]},"5":{"code":"king","ai":false,"position":[11,5]},"4":{"code":"bishop","ai":false,"position":[11,4]},"3":{"code":"knight","ai":false,"position":[11,3]},"2":{"code":"rook","ai":false,"position":[11,2]}}]'

        y = json.loads(original)
        

        for row in y:
            if row == []:
                continue

            for column, values in row.items():
                if values['ai'] == True:
                    fill('b', values['code'], values['position'])
                else:
                    fill('w', values['code'], values['position'])

        """Track aspects of the game"""
        #track which pieces have been taken
        self.white_taken = []
        self.black_taken = []
        
        #track whose turn it is (white always starts)
        self.current_turn = "b"

        maxCounter = 0
        self.finalP = 0
        self.finalQ = 0
        for p in range(4):
            for q in range(4):
                counter = 0
                hasAiPiece = False
                for i in range(8):
                    for j in range(8):
                        if okPQ(i, j):
                            if chessboard1[i+p][j+q] != '0-0':
                                counter += 1
                            if chessboard1[i+p][j+q][0] == 'b':
                                hasAiPiece = True
                if counter > maxCounter and hasAiPiece:
                    maxCounter = counter
                    self.finalP = p
                    self.finalQ = q

        for i in range(8):
            for j in range(8):
                self.chessboard[i][j] = chessboard1[i+self.finalP][j+self.finalQ]

        self.game_over = False
            
    def see_board(self):
        """see the current state of the chessboard"""
        for i in self.chessboard:
            print(i)

    
    def whose_turn(self):
        #print(self.current_turn + " to move")
        return self.current_turn

    
    def recommend_move(self, depth_override = None):
        """
        Use the AI to recommend a move (will not actually the move)
        """
        if not depth_override:
            depth_override = self.ai_depth

        self.tree_generator(depth_override)
        return self.minimax(self.current_game_state, 0)

    def make_move_ai(self, depth_override = None):
        """
        Let the AI make the move
        """
        if not depth_override:
            depth_override = self.ai_depth

        myoutput = self.recommend_move(depth_override)
        start  = myoutput[2]
        finish = myoutput[3]
        if ((int(finish[1]) - 8) * (-1) + self.finalP < 2 and ord(finish[0]) - 97 + self.finalQ < 2) or ((int(finish[1]) - 8) * (-1) + self.finalP > 9 and ord(finish[0]) - 97 + self.finalQ < 2) or ((int(finish[1]) - 8) * (-1) + self.finalP < 2 and ord(finish[0]) - 97 + self.finalQ > 9) or ((int(finish[1]) - 8) * (-1) + self.finalP > 9 and ord(finish[0]) - 97 + self.finalQ > 9):
            self.invalidPQ.append([self.finalP, self.finalQ])
            return self.invalidPQ
        else:
            data = {}
            data['start'] = [(int(start[1]) - 8) * (-1) + self.finalP, ord(start[0]) - 97 + self.finalQ]
            data['finish'] = [(int(finish[1]) - 8) * (-1) + self.finalP, ord(finish[0]) - 97 + self.finalQ]
            json_data = json.dumps(data)
            return json_data


    def make_move(self, start, finish):
        """
        Make a move
        
        input:
        starting coordinate: example "e4"
        ending coordinate: example "e5"
        
        output:
        "Move success" or "Move invalid", self.chessboard is updated with the move made
        
        Uses the RulesEnforcer() to make sure that the move is valid
        
        """
        
        #map start and finish to gameboard coordinates
        start  = RulesEnforcer.coordinate_mapper(start)
        finish = RulesEnforcer.coordinate_mapper(finish)
        
        #need to move alot of this logic to the rules enforcer
        start_cor0  = start[0]
        start_cor1  = start[1]
        
        finish_cor0 = finish[0]
        finish_cor1 = finish[1]
        
        #check if destination is white, black or empty
        start_color = self.chessboard[start_cor0][start_cor1].split('-')[0]
        start_piece = self.chessboard[start_cor0][start_cor1].split('-')[1]
        
        #check if destination is white, black or empty
        destination_color = self.chessboard[finish_cor0][finish_cor1].split('-')[0]
        destination_piece = self.chessboard[finish_cor0][finish_cor1].split('-')[1]
        
        #cannot move if starting square is empty
        if start_color == '0':
            return "Starting square is empty!"
        
        #cannot move the other person's piece
        if self.current_turn != start_color:
            return "Cannot move the other person's piece!"
        
        #cannot take your own piece 
        if self.current_turn == destination_color:
            return "invalid move, cannot take your own piece!"
        elif self.current_turn != destination_color and destination_color != '0':
            if destination_piece == 'k':
                self.game_over = True
                return "game over, " + self.current_turn + " has won"
            elif self.current_turn == 'w':
                self.black_taken.append(destination_piece)
            elif self.current_turn == 'b':
                self.white_taken.append(destination_piece)     
        else:
            pass
        
        mypiece = self.chessboard[start_cor0][start_cor1]
        self.chessboard[start_cor0][start_cor1] = '0-0'
        self.chessboard[finish_cor0][finish_cor1] = mypiece
        
        #if the move is a success, change the turn state
        if self.current_turn == "w":
            self.current_turn = "b"
        elif self.current_turn == "b":
            self.current_turn = "w"
        
        return self.chessboard


    
    def current_position_score(self):
        """
        Get the position score of the current game being played
        """
        return self.position_evaluator(self.chessboard)


