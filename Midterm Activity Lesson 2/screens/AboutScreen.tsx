import React from "react";
import { View, Text, StyleSheet } from "react-native";

export default function AboutScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>About Student Portal</Text>

      <View style={styles.content}>
        <Text style={styles.text}>
          This Student Portal application was developed using React Native.
        </Text>

        <Text style={styles.text}>
          It demonstrates the use of multiple screens, Flexbox, StyleSheet,
          and Stack Navigation.
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#EAF4FF",
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },

  title: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 25,
  },

  content: {
    backgroundColor: "#FFFFFF",
    padding: 20,
    borderRadius: 10,
    width: "90%",
  },

  text: {
    fontSize: 16,
    color: "#333333",
    textAlign: "center",
    marginBottom: 12,
  },
});