"""chess game"""
import math
import itertools
import json

from game import RulesEnforcer
from ai import ChessAi


class ChessGame(RulesEnforcer,ChessAi):
    def __init__(self, payload, invalidPQ):
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
        
        ChessAi.__init__(self, payload['level'])
        RulesEnforcer.__init__(self)
        #super(ChessGame, self).__init__()

        self.ai_depth = payload['level']

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

        y =  payload['board']
        for row in y:
            for column, values in row.items() if type(row) is dict else enumerate(row):
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
                if okPQ(p, q):
                    counter = 0
                    hasAiPiece = False
                    hasKing = False
                    hasOpponentPiece = False
                    for i in range(8):
                        for j in range(8):
                            if chessboard1[i+p][j+q] != '0-0':
                                counter += 1
                            if chessboard1[i+p][j+q][0] == 'b':
                                hasAiPiece = True
                            if chessboard1[i+p][j+q][2] == 'k' and chessboard1[i+p][j+q][0] == 'w':
                                hasKing = True
                            if chessboard1[i+p][j+q][0] == 'w':
                                hasOpponentPiece = True
                    if counter > maxCounter and hasAiPiece and hasKing and hasOpponentPiece:
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


