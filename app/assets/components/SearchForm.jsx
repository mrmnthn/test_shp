import React, { Component } from "react";
import { Button, Divider, Form, Grid } from "semantic-ui-react";
import axios from "axios";
import ResultTable from "./ResultTable";

class SearchForm extends Component {
  constructor() {
    super();
    this.state = {
      airports: [],
      from: {
        value: "",
        text: "",
      },
      to: {
        value: "",
        text: "",
      },
      bestFlights: [],
    };
  }

  componentDidMount() {
    this.getAirports();
  }

  getAirports() {
    axios.get(`http://localhost/api/airports`).then((airports) => {
      this.setState({ airports: airports.data });
    });
  }

  getBestPrice() {
    const { value: fromVal } = this.state.from;
    const { value: toVal } = this.state.to;
    axios
      .get(`http://localhost/api/bestflights`, {
        params: {
          fromVal,
          toVal,
        },
      })
      .then((bestFlights) => {
        this.setState({ bestFlights: bestFlights.data });
      });
  }

  handleChange = (name, value, e) => {
    this.setState({ [name]: { value, text: e.target.innerText } });
  };

  onSubmit = (e) => {
    e.preventDefault();
    this.getBestPrice();
  };

  resetSearch = () => {
    this.setState({
      from: {
        value: "",
        text: "",
      },
      to: {
        value: "",
        text: "",
      },
      bestFlights: [],
    });
  };

  render() {
    const { airports, from, to, bestFlights } = this.state;
    return (
      <>
        <Grid columns={3}>
          <Grid.Row>
            <Grid.Column>
              <Form onSubmit={this.onSubmit}>
                <Form.Select
                  value={from.value}
                  name="from"
                  fluid
                  label="Departure Airport"
                  options={airports}
                  placeholder="Select an airport"
                  onChange={(e, { value, name }) =>
                    this.handleChange(name, value, e)
                  }
                />
                <Form.Select
                  value={to.value}
                  name="to"
                  fluid
                  label="Arrival Airport"
                  options={airports}
                  placeholder="Select an airport"
                  onChange={(e, { value, name }) =>
                    this.handleChange(name, value, e)
                  }
                />
                <Button
                  color="green"
                  type="submit"
                  disabled={!from.value || !to.value}
                >
                  Search
                </Button>
                <Button type="reset" color="pink" onClick={this.resetSearch}>
                  Reset
                </Button>
              </Form>
            </Grid.Column>
          </Grid.Row>
        </Grid>
        <Divider />
        <ResultTable bestFlights={bestFlights} />
      </>
    );
  }
}
export default SearchForm;
