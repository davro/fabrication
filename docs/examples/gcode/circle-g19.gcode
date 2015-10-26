(GCode fabricated on Mon, 26 Oct 2015 23:34:39 +0000 http://davro.net)
G90 (absolute mode)
F1000  (Feed Rate)
S0  (Spindle Speed)
G21 (Metric mm)

(circle)
G0 Y-10 Z15 (rapid start)
G1 X0 (axis spindle start point)
G19 G2 Y-10 Z15 J10 K0.00 X15
G0 X10 (axis spindle safe point)
(/circle)
M2
