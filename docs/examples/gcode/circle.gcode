(GCode fabricated on Mon, 26 Oct 2015 20:28:06 +0000 http://davro.net)
G90 (absolute mode)
F2000  (Feed Rate)
S0  (Spindle Speed)
G21 (Metric mm)

(circle x=15 y=15 z=0 radius=10 plane=G17 ofx=5 ofy=5)
G0 X5 Y15 (rapid start)
G1 Z0
G17 G2 X5 Y15 I10 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)
M2
