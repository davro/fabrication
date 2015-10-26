(GCode fabricated on Mon, 26 Oct 2015 23:23:59 +0000 http://davro.net)
G90 (absolute mode)
F1000  (Feed Rate)
S0  (Spindle Speed)
G21 (Metric mm)

(circle x=15 y=15 z=0 radius=10 plane=G19 of=5 )
G0 Y5 Z15 (rapid start)
G1 X0 (axis spindle start point)
G19 G2 Y5 Z15 J10 K0.00 X15
G0 X10 (axis spindle safe point)
(/circle)
M2
